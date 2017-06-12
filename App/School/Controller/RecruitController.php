<?php

namespace App\School\Controller;


use Controller;
use View;
use Request;
use App\School\Tool\AJAX;
use App\School\Tool\Func;
use App\School\Middleware\L;

use App\School\Model\RecruitModel;
use App\School\Model\RecruitStudentsModel;

use PHPExcel_Writer_Excel2007;
use PHPExcel;

class RecruitController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;
        

    }

    function get($id,RecruitModel $model){

        !$id && AJAX::success(['info'=>[]]);

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$recruit]);
    }

    function add($name,$time,$address,$number,$comment,RecruitModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $data = Request::getInstance()->post(['title','date','time','address','number','comment']);
        count($data) != 6 && AJAX::error_i18n('param_error');

        $data['create_user'] = $data['update_user'] = $this->L->id;
        $data['create_time'] = $data['update_time'] = TIME_NOW;

        $succ = $model->set($data)->add()->getStatus();
        !$succ && AJAX::error_i18n('param_error');
        
        AJAX::success();

    }

    function lists_w($page = 1,$limit = 30,RecruitModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/recruit/get','upd'=>'/recruit/change','del'=>'/recruit/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->recruit->title)=>['class'=>'tc'],
            ($this->lang->recruit->date)=>['class'=>'tc'],
            ($this->lang->recruit->number)=>['class'=>'tc'],
            ($this->lang->recruit->status)=>['class'=>'tc'],

            '_opt'=>['class'=>'tc'],
        ];

        $title = $this->lang->language == 'cn' ? 'title' : 'title_en';
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            $title=>['class'=>'tc'],
            'date'=>['class'=>'tc'],
            'number'=>['class'=>'tc'],
            'status'=>['class'=>'tc','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];


        $out['lang'] = $this->lang->language;

        $list = $model->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->title = $this->lang->language == 'cn' ? $v->title : $v->title_en;
        }

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);

    }

    function change($id,$status = null,RecruitModel $model,$title,$number,$address,$comment,$date){

        if($id){
            $recruit = $model->find($id);
            !$recruit && AJAX::error_i18n('no_data');

            $data = Request::getInstance()->request($model->field);

            $data['update_user'] = $this->L->id;
            $data['update_time'] = TIME_NOW;
            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');

        }else{
            
            $data['create_user'] = $data['update_user'] = $this->L->id;
            $data['create_time'] = $data['update_time'] = TIME_NOW;
            $data = Request::getInstance()->request($model->field);
            unset($data['id']);
            $data['number'] = $data['number']?$data['number']:'0';
            $model->set($data)->add()->getStatus();

        }

        

        AJAX::success();

    }

    function schange($id = 0,RecruitStudentsModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        $data = Request::getInstance()->post(['parent_name','parent_name_en','student_name','student_name_en','address','age','phone','weight','height']);
        $data['update_time'] = TIME_NOW;
        
        !$model->set($data)->where(['id'=>$id])->save()->getStatus() && AJAX::error_i18n('save_failed');

        AJAX::success();

    }

    

    function post(RecruitStudentsModel $model){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        !$wc_openid && AJAX::error('请在微信操作！');

        $data = Request::getInstance()->post(['parent_name','parent_name_en','student_name','student_name_en','address','age','phone','weight','height','recruit_id']);
        $data['openid'] = $wc_openid;
        $data['update_time'] = $data['create_time'] = TIME_NOW;

        $out['out_trade_no'] =  $data['out_trade_no'] = $out_trade_no = date('Ymdhis').Func::randWord(10,3);
        
        !$model->set($data)->add()->getStatus() && AJAX::error_i18n('save_failed');

        AJAX::success($out);

    }


    function del($id = 0,RecruitModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $recruit = $model->remove($id);
        AJAX::success();

    }


    

    function slists_w(RecruitStudentsModel $model,$id,$ispaid,$page = 1,$limit = 30){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/recruit/sget','upd'=>'/recruit/schange','del'=>'/recruit/sdel'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->recruit->parent_name)=>['class'=>'tc'],
            ($this->lang->recruit->student_name)=>['class'=>'tc'],
            ($this->lang->recruit->phone)=>['class'=>'tc'],
            ($this->lang->recruit->age)=>['class'=>'tc'],
            ($this->lang->recruit->ispaid)=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $name = $this->lang->language == 'cn' ? 'parent_name' : 'parent_name_en';
        $sname = $this->lang->language == 'cn' ? 'student_name' : 'student_name_en';
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            $name=>['class'=>'tc'],
            $sname=>['class'=>'tc'],
            'phone'=>['class'=>'tc'],
            'age'=>['class'=>'tc'],
            'ispaid'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];


        $out['lang'] = $this->lang->language;

        $where = [];
        if($id)$where['recruit_id'] = $id;
        if($ispaid){

            $where[] = $ispaid == 1 ? ['pay_time > 0'] : ['pay_time = 0'];
        }
        $list = $model->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->ispaid = $v->pay_time ? 'Yes' : 'No';
        }

        $out['list']  = $list;
        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);


    }



    function sget($id,RecruitStudentsModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$recruit]);
    }


    function sdel($id = 0,RecruitStudentsModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $recruit = $model->remove($id);
        AJAX::success();

    }

    function view_exam_list(RecruitModel $model){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        !$wc_openid && header('Location:/wc/roll?state=recruit');
        $list = $model->selectExcept('comment','comment_en')->where(['status'=>1])->select('*',$name)->order('date','time')->get()->toArray();

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }

    function view_exam_info(RecruitModel $model,$id){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        !$wc_openid && header('Location:/wc/roll?state=recruit');

        $info = $model->find($id);
        !$info && die();
        $info->comment = str_replace("\n","<br>",$info->comment);
        $info->comment_en = str_replace("\n","<br>",$info->comment_en);

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }

    function view_exam_submit(){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        // !$wc_openid && header('Location:/wc/roll?state=recruit');

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }

    function view_my_submit(RecruitStudentsModel $model){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        !$wc_openid && header('Location:/wc/roll?state=recruit');

        $list = $model->select('*','recruit.title','recruit.title_en')->where(['openid'=>$wc_openid,['pay_time > 0']])->order('pay_time','DESC')->get()->toArray();
        if(!$list){
            include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'_none.php';
        }
        else{
            include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
        }
        
    }
    function view_my_submit_none(){

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }

    function language(){

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }


    function zout($recruit_id,$is_paid,$start,$end){

        $this->L->check_type([5,6,7]);

        require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Settings.php';
        require_once VENDOR_ROOT.'os/php-excel/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        
        $sheet = $objPHPExcel->getActiveSheet();

        $model = RecruitStudentsModel::getInstance();

        if($recruit_id)$model->where(['recruit_id'=>$recruit_id]);
        if($is_paid == 1)$model->where('pay_time>0');
        else if($is_paid == 2)$model->where(['pay_time'=>0]);

        $start = strtotime($start);
        $end = strtotime($end);
        if($start)$model->where('create_time>=%n',$start);
        if($end)$model->where('create_time<%n',$end+24*3600);

        $list = $model->order('create_time')->get()->toArray();

        if(!$list)AJAX::success('无数据/NO DATA');

        $sheet->setCellValue('B1', '家长名字');
        $sheet->setCellValue('C1', 'Parent Name');
        $sheet->setCellValue('D1', '学生名字');
        $sheet->setCellValue('E1', 'Student Name');
        $sheet->setCellValue('F1', '电话/Phone');
        $sheet->setCellValue('G1', '地址/Address');
        $sheet->setCellValue('H1', '年龄/Age');
        $sheet->setCellValue('I1', '体重/Weight');
        $sheet->setCellValue('J1', '身高/Height');
        $sheet->setCellValue('K1', '填表时间/Apply Date');
        $sheet->setCellValue('L1', '付款时间/Pay Time');


        foreach($list as $k=>$v){
            $sheet->setCellValue('A'.($k+2), $k+1);
            $sheet->setCellValue('B'.($k+2), $v->parent_name);
            $sheet->setCellValue('C'.($k+2), $v->parent_name_en);
            $sheet->setCellValue('D'.($k+2), $v->student_name);
            $sheet->setCellValue('E'.($k+2), $v->student_name_en);
            $sheet->setCellValue('F'.($k+2), $v->phone);
            $sheet->setCellValue('G'.($k+2), $v->address);
            $sheet->setCellValue('H'.($k+2), $v->age);
            $sheet->setCellValue('I'.($k+2), $v->weight.' KG');
            $sheet->setCellValue('J'.($k+2), $v->height.' CM');
            $sheet->setCellValue('K'.($k+2), date('Y/m/d',$v->create_time));
            $sheet->setCellValue('L'.($k+2), $v->pay_time?date('Y/m/d',$v->pay_time):'未付款/Not Paid');

        }


        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(BASE_ROOT."log/05featuredemo.xlsx");


        $filename=realpath(BASE_ROOT."log/05featuredemo.xlsx"); //文件名

        header( "Content-type:  application/octet-stream "); 
        header( "Accept-Ranges:  bytes "); 
        header( "Accept-Length: " .filesize($filename));
        header( "Content-Disposition:  attachment;  filename= report.xlsx");
        echo file_get_contents($filename);
        // readfile($filename); 

        // echo $objWriter->generateHTMLHeader();
        // echo $objWriter->generateStyles(false); // do not write <style> and </style>
        // echo $objWriter->generateSheetData();
        // echo $objWriter->generateHTMLFooter();

    }
    
}