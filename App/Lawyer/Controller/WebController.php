<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Lawyer\Middleware\L2;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\FastQuestionModel;
use App\Lawyer\Model\VisaSendModel;
use Model;

use App\Lawyer\Model\VisaWorkModel;
use App\Lawyer\Model\VisaFamilyModel;
use App\Lawyer\Model\VisaRefuseModel;
use App\Lawyer\Model\VisaTravelModel;
use App\Lawyer\Model\VisaMarryModel;
use App\Lawyer\Model\VisaGraduateModel;
use App\Lawyer\Model\VisaStudentModel;
use App\Lawyer\Model\VisaPerpetualModel;
use App\Lawyer\Model\VisaSelectModel;
use App\Lawyer\Model\VisaSelectOptionModel;
use App\Lawyer\Model\VisaTechnologyModel;
use App\Lawyer\Model\VisaTechnologyOptionModel;
use App\Lawyer\Model\VisaBusinessModel;
use App\Lawyer\Model\VisaBusinessOptionModel;


class WebController extends Controller{


    function __construct(){

        $this->L = L2::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    /** 给密码加密
     * 2333
     * @param mixed $password 
     * @param mixed $salt 
     * @return mixed 
     */
    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }
    /** 生成登录TOKEN
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function encrypt_token($info){
        return Func::randWord().Func::aes_encode(Func::randWord().base64_encode(sha1($info->password.$this->salt.TIME_NOW).'|'.$info->id.'|'.TIME_NOW));
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,LawyerModel $model,$cookie = null){


        //检查参数是否存在
        !$phone && AJAX::error('账号不能为空！');
        !$password && AJAX::error('密码不能为空！');
        
        //找到对应手机号的用户
        $info = $model->where('phone=%n',$phone)->find();
        !$info && AJAX::error('用户不存在');

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        # 验证密码 加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        $encryptedPassword = $this->encrypt_password($password,'');
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        //输出登录返回信息
        $this->_out_info($info);


    }

    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info){
        
        $user_token = $this->encrypt_token($info);
        
        $this->cookie && Response::getSingleInstance()->cookie('lawyer_token',$user_token,0);
        
        $out = [
            'lawyer_token'=>$user_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name
            
        ];
        
        AJAX::success($out);
    }


    /** 获取我的信息
     * getMyInfo
     * @return mixed 
     */
    function getMyInfo(){
        
        !$this->L->id && AJAX::error('未登录');

        $info = new stdClass;

        $info->name = $this->L->userInfo->name;
        $info->avatar = $this->L->userInfo->avatar;
        $info->type = $this->L->userInfo->type;
        $info->id = $this->L->id;

        $out['info'] = $info;
        
        AJAX::success($out);
    
    }


    /** 获取聊天记录
     * getChatList
     * @param mixed $id 
     * @return mixed 
     */
    function getChatList($id,ConsultModel $consultModel,$page = 1,$limit = 10){

        !$this->L->id && AJAX::error('未登录');
        
        $where['lawyer_id'] = $this->L->id;
        $where['user_id'] = $id;

        $list = $consultModel->select('*','lawyer.avatar>lawyer_avatar','user.avatar>user_avatar','user.name')->where($where)->page($page,$limit)->order('create_time desc')->get()->toArray();
        

        $consultModel->where($where)->where(['which'=>0])->set(['isread'=>1])->save();

        $list2 = [];
        foreach($list as $v){

            
            
            $list2[$v->create_time][] = $v;

        }
        
        ksort($list2);

        $list = [];

        foreach($list2 as $v){
            foreach($v as &$j)$j->create_time = date('m-d H:i:s',$j->create_time);
            $list = array_merge($list,$v);

        }

        $out['list'] = $list;

        AJAX::success($out);

    }

    function getNewChatList($id,ConsultModel $consultModel){
        !$this->L->id && AJAX::error('未登录');
        
        $where['lawyer_id'] = $this->L->id;
        $where['user_id'] = $id;
        $where['which'] = 0;
        $where['isread'] = 0;

        $list = $consultModel->select('*','lawyer.avatar>lawyer_avatar','user.avatar>user_avatar')->where($where)->order('create_time')->get()->toArray();

        foreach($list as $v){

            $v->create_time = date('m-d H:i:s',$v->create_time);

        }
        $out['list'] = $list;
        AJAX::success($out);

    }
    

    /** 我的咨询列表
     * getChatList
     * @return mixed 
     */
    function getMyChat(ConsultModel $consultModel){

        !$this->L->id && AJAX::error('未登录');
        
        $where['lawyer_id'] = $this->L->id;
        $consultModel->distinct();

        $lawyer_list = $consultModel->where($where)->get_field('user_id')->toArray();
        
        $list = [];
        foreach($lawyer_list as $v){

            $info = $consultModel->select('content','create_time','user_id','user.name','user.avatar')->where($where)->where(['user_id'=>$v])->order('create_time desc')->find();
            $info->isread = $consultModel->where($where)->where(['lawyer_id'=>$v])->where(['which'=>1,'isread'=>0])->find() ?'0':'1';

            $list[$info->create_time][] = $info;

        }

        krsort($list);
        $list2 = [];
        foreach($list as $v){

            $list2 = array_merge($list2,$v);
        }

        $out['list'] = $list2;

        AJAX::success($out);

    }



    /** 回复问题
     * sendQuestionToUser
     * @param mixed $id 
     * @param mixed $message 
     * @return mixed 
     */
    function sendQuestionToUser($id,$message,ConsultModel $consultModel){
        
        !$this->L->id && AJAX::error('未登录');

        !$message && AJAX::error('消息不能为空！');

        $word_count = mb_strlen($message);

        $data['user_id'] = $id;
        $data['lawyer_id'] = $this->L->id;
        $data['which'] = 1;
        $data['create_time'] = TIME_NOW;
        $data['content'] = $message;
        $data['word_count'] = $word_count;

        DB::start();

        $consultModel->set($data)->add();
        Func::push($id,'律师回复了你',['type'=>'2','lawyer_id'=>$this->L->id]);

        DB::commit();

        AJAX::success();

    }



    function getAllVisa(VisaSendModel $model){
        
        !$this->L->id && AJAX::error('未登录');

        $where['lawyer_id'] = $this->L->id;

        $list = $model->group('type')->where($where)->select('COUNT(*) AS `count`,`type`','RAW')->get('type')->toArray();

        $data['work'] = $list['work']?$list['work']->count:'0';
        $data['family'] = $list['family']?$list['family']->count:'0';
        $data['refuse'] = $list['refuse']?$list['refuse']->count:'0';
        $data['travel'] = $list['travel']?$list['travel']->count:'0';
        $data['marry'] = $list['marry']?$list['marry']->count:'0';
        $data['graduate'] = $list['graduate']?$list['graduate']->count:'0';
        $data['student'] = $list['student']?$list['student']->count:'0';
        $data['perpetual'] = $list['perpetual']?$list['perpetual']->count:'0';
        $data['technology'] = $list['technology']?$list['technology']->count:'0';
        $data['business'] = $list['business']?$list['business']->count:'0';

        $out = $data;

        AJAX::success($out);
        
    }

    function getVisaByVisaType(VisaSendModel $model,$type){

        !$this->L->id && AJAX::error('未登录');

        $types = ['work','family','refuse','travel','marry','graduate','student','perpetual','technology','business'];
        
        if(!in_array($type,$types))AJAX::error('未知的签证类型！');

        $where['lawyer_id'] = $this->L->id;

        if($type)$where['type'] = $type;
        
        $list = $model->select('*','user.avatar','user.name')->where($where)->order('create_time')->get()->toArray();

        foreach($list as &$v){
            $v->date = date('Y-m-d',$v->create_time);
        }
        

        $out['list'] = $list;

        AJAX::success($out);
        
    }




    # 工作签证
    function getWork(VisaWorkModel $model,$user_id){
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);
        $out['info'] = $info;
        AJAX::success($out);
    }

    # 家庭团聚签证
    function getFamily(VisaFamilyModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }

    # 拒签上诉
    function getRefuse(VisaRefuseModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }

    # 旅游签证
    function getTravel(VisaTravelModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }

    # 配偶签证
    function getMarry(VisaMarryModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    
    # 学生毕业签证
    function getGraduate(VisaGraduateModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    
    # 学生签证/陪读
    function getStudent(VisaStudentModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    
    # 永久签证
    function getPerpetual(VisaPerpetualModel $model,$user_id){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    
    # 技术移民签证
    function getTechnology($user_id,
        VisaTechnologyModel $model,
        VisaTechnologyOptionModel $optionModel,
        VisaSelectModel $visaSelectModel
        ){

        !$this->L->id && AJAX::error('未登录');
        $info = $model->find($user_id);

        if($info){

            $option = $optionModel->where(['technology_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>1])->group('id')->order('ord')->get()->toArray();

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            if($info){
                $v->value = $option[$v->id]->value;
                if(!$v->value)$v->value = '';
            }else{
                $v->value = '';
            }
        }

        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        $out['select'] = $select;
        AJAX::success($out);

    }
    
    # 商业签证
    function getBusiness($user_id,
        VisaBusinessModel $model,
        VisaBusinessOptionModel $optionModel,
        VisaSelectModel $visaSelectModel
        ){

        !$this->L->id && AJAX::error('未登录');
        $info = $model->find($user_id);

        if($info){

            $option = $optionModel->where(['business_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>2])->group('id')->order('ord')->get()->toArray();

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            if($info){
                $v->value = $option[$v->id]->value;
                if(!$v->value)$v->value = '';
            }else{
                $v->value = '';
            }
        }

        $info = AdminFunc::get($model,$user_id);

        $out['info'] = $info;
        $out['select'] = $select;
        AJAX::success($out);

    }
    


    function addPrice($type,$user_id,$price){

        !$this->L->id && AJAX::error('未登录');

        $types = ['work','family','refuse','travel','marry','graduate','student','perpetual','technology','business'];

        if(!in_array($type,$types))AJAX::error('未知的签证类型！');

        if(!$price)AJAX::success('价格不能为空！');

        $save = Model::copyMutiInstance('visa_'.$type)->set(['price'=>$price])->where(['id'=>$user_id])->save()->getStatus();

        !$save && AJAX::error('用户没有填写签证！');
        AJAX::success();

    }

}