<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\FastQuestionModel;



class LawyerController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    /** 获取律师列表
     * getLawyerList
     * @param mixed $type 律师类型
     * @param mixed $page
     * @param mixed $limit
     * @return mixed 
     */
    function getLawyerList($type = 0 ,$pgae = 1 ,$limit = 10,LawyerModel $model){

        $where['type'] = $type;
        $where['active'] = 1;
        $list = $model->select('id','avatar','name','description','company','site','fee_star','average_reply','feedback_star','type','online_time')->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        foreach($list as &$l){
            $l->average_reply = Func::time_zcalculate($l->average_reply);
        }
        
        $out['list'] = $list;
        AJAX::success($out);

    }

    /** 获取律师详情
     * getLawyerInfo
     * @param mixed $id 律师的ID
     * @param mixed $model 
     * @return mixed 
     */
    function getLawyerInfo($id = 0,LawyerModel $model){

        $where['id'] = $id;
        $where['active'] = 1;
        $info = $model->select('id','avatar','name','description','company','site','fee_star','average_reply','feedback_star','type','online_time')->where($where)->find();
        !$info && AJAX::error('律师不存在！');

        $info->average_reply = Func::time_zcalculate($info->average_reply);

        $out['info'] = $info;
        AJAX::success($out);

    }

    /** 检查律师
     * checkLawyerAuth
     * @param mixed $id 
     * @param mixed $model 
     * @param mixed $limitModel 
     * @param mixed $ajax 
     * @return mixed 
     */
    function checkLawyerAuth($id,$ajax = true){

        $model = LawyerModel::copyMutiInstance();
        $limitModel = UserConsultLimitModel::copyMutiInstance();
        $consultModel = ConsultModel ::copyMutiInstance();

        !$this->L->id && AJAX::error('未登录');

        $where['id'] = $id;
        $where['active'] = 1;
        $info = $model->where($where)->find();
        !$info && AJAX::error('律师不存在！');

        $type = $info->type;

        $where = ['user_id'=>$this->L->id];
        $where['rule.type'] = $type;

        $auth = $limitModel->select('*','rule.hours')->where($where)->find();

        !$auth && AJAX::error('请开通会员！');

        $auth->death_time < TIME_NOW && AJAX::error('会员已到期，请重新开通会员！');
        $auth->word_count == 0 && AJAX::error('总字数已用完，请重新开通会员！');
        $auth->question_count == 0 && AJAX::error('问题总数已用完，请重新开通会员！');

        $lawyer_id = $this->L->userInfo->lawyer_id;
        if($lawyer_id && $lawyer_id != $id){
            if(!$consultModel->where(['user_id'=>$this->L->id,'lawyer_id'=>$lawyer_id,'which'=>1])->find()){
                $consult = $consultModel->where(['user_id'=>$this->L->id,'lawyer_id'=>$lawyer_id,'which'=>0])->order('create_time')->find();
                if($consult){
                    $consult->create_time + 3600 * $auth->hours > TIME_NOW && AJAX::error('律师已绑定，'.$auth->hours.'小时后未回复可以更换律师！');
                }else{
                    AJAX::error('律师已绑定，请咨询您绑定的律师！');
                }
            }
        }


        $ajax && AJAX::success();

        if(!$ajax){
            unset($auth->hours);
            $obj = new stdClass;
            $obj->auth = $auth;

            return $obj;
        }
        
    }


    /** 发送问题
     * sendQuestionToLawyer
     * @param mixed $id 
     * @param mixed $message 
     * @param mixed $lawyerModel 
     * @param mixed $consultModel 
     * @return mixed 
     */
    function sendQuestionToLawyer($id,$message,LawyerModel $lawyerModel,ConsultModel $consultModel){
        
        !$this->L->id && AJAX::error('未登录');

        $mee = $this->checkLawyerAuth($id,false);

        $this->L->userInfo->lawyer_id = $id;
        $this->L->userInfo->save();

        $word_count = mb_strlen($message);

        $data['user_id'] = $this->L->id;
        $data['lawyer_id'] = $id;
        $data['which'] = 0;
        $data['create_time'] = TIME_NOW;
        $data['content'] = $message;
        $data['word_count'] = $word_count;

        DB::start();

        $mee->auth->word_count != -1 && $mee->auth->word_count < $word_count && AJAX::error('提问失败，剩余字数不足！');

        $mee->auth->word_count != -1 && $mee->auth->word_count -= $word_count;
        $mee->auth->question_count != -1 && $mee->auth->question_count -= 1;
        $mee->auth->save();
        

        $consultModel->set($data)->add();
  

        DB::commit();

        AJAX::success();

    }


    /** 获取法律快速提问列表
     * FastQuestionList
     * @return mixed 
     */
    function fastQuestionList(FastQuestionModel $model){

        $list = $model->order('top desc')->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);

    }


    /** 获取聊天记录
     * getChatList
     * @param mixed $id 
     * @param mixed $consultModel 
     * @return mixed 
     */
    function getChatList($id,ConsultModel $consultModel){

        !$this->L->id && AJAX::error('未登录');
        
        $where['user_id'] = $this->L->id;
        $where['lawyer_id'] = $id;

        $list = $consultModel->where($where)->order('create_time')->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);

    }


    /** 我的咨询列表
     * getChatList
     * @return mixed 
     */
    function getMyChat(ConsultModel $consultModel){

        !$this->L->id && AJAX::error('未登录');
        
        $where['user_id'] = $this->L->id;

        $list = $consultModel->select(['%F,%F,%F,MAX(%F)','lawyer_id','lawyer.name','lawyer.avatar','create_time'],'RAW')->where($where)->group('lawyer_id')->order('create_time desc')->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);

    }





    function admin_lawyer(LawyerModel $model,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(75);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/lawyer/admin_lawyer_get',
                'upd'   => '/lawyer/admin_lawyer_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/lawyer/admin_lawyer_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '律师ID',
                '类型',
                '名字',
                '启用',

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'typeName',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],

            ];
            

        # 列表内容
        $where = [];

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        $types = ['法律','留学转学','签证'];

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->typeName = $types[$v->type];
        }

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_lawyer_get(LawyerModel $model,$id){

        $this->L->adminPermissionCheck(75);

        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/lawyer/admin_lawyer_get',
                'upd'   => '/lawyer/admin_lawyer_upd',
                'back'  => 'staff/lawyer',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/lawyer/admin_lawyer_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '类型',
                    'name'  =>  'type',
                    'type'  =>  'select',
                    'default'=> '0',
                    'option'=>  [
                        '0' =>  '法律',
                        '1' =>  '留学转学',
                        '2' =>  '签证',
                    ]
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                [
                    'title' =>  '在线时间',
                    'name'  =>  'online_time',
                ],
                [
                    'title' =>  '优先级',
                    'name'  =>  'top',
                    'size'  =>  '2',
                    'default'=> '0'	
                ],
                [
                    'title' =>  '公司推荐',
                    'name'  =>  'company',
                ],
                [
                    'title' =>  '资质政府网站',
                    'name'  =>  'site',
                ],
                [
                    'title' =>  '平均回复时间(秒)',
                    'name'  =>  'average_reply',
                    'default'=> '0'	,
                    'size'  =>  '2',
                ],
                [
                    'title' =>  '星级',
                    'name'  =>  'feedback_star',
                    'size'  =>  '2',             	
                ],
                [
                    'title' =>  '简介',
                    'name'  =>  'description',
                    'type'  =>  'textarea'         	
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_lawyer_upd(LawyerModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(75);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        if(!$id){
            $data['password'] = UserController::getSingleInstance()->encrypt_password($pwd,'');
        }elseif($pwd){
            $data['password'] = UserController::getSingleInstance()->encrypt_password($pwd,'');
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_lawyer_del(LawyerModel $model,$id){
        $this->L->adminPermissionCheck(75);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }
    

}