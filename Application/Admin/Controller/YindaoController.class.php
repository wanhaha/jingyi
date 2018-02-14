<?php
namespace Admin\Controller;
use Think\Controller;
class YindaoController extends CommonController {

    public function _initialize(){
        parent::_initialize();
        if(session('admin_key_id') !=1){
            if(!in_array('3',session('admin_key_auth'))){
                session('[destroy]');
                $this->redirect('Login/index',array(), 1, '无权限...');
            }
        }
    }

    public function index(){
        $Model=M("yindao");
        $order="tui asc";
        $list = $Model->order($order)->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function add(){
        $this->display();
    }

    public function addpost(){
        $Model = M('yindao');
        $data = $Model->create();
        //$data['content']=stripslashes($_POST['content']);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts      =     array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/yindao/'; // 设置附件上传目录
        $upload-> saveName  =   array('uniqid','');
        $upload-> ischeckfile  =   true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach($info as $file){
                $data[$file['key']]=$file['savepath'].$file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res=$Model->add($data);
        if($res){
            admin_log('添加引导图，编号为：'.$res);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Yindao/add");
        }else{
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Yindao/add");
        }
    }

    public function edit(){
        $id=$_GET["id"];
        $Model=M("Yindao");
        $where="id=".$id;
        $res=$Model->where($where)->find();
        $this->assign('res',$res);
        $this->display();
    }

    public function editpost(){
        $Model = M('Yindao');
        $data = $Model->create();
        //$data['content']=stripslashes($_POST['content']);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts      =     array('jpg',  'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/yindao/'; // 设置附件上传目录
        $upload-> saveName  =   array('uniqid','');
        //$upload-> ischeckfile  =   true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            // $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach($info as $file){
                $data[$file['key']]=$file['savepath'].$file['savename'];
            }
            $res=$Model->where("id=".$_POST["id"])->find();
            unlink($res["images"]);
        } // 保存表单数据包括附件数据<br />

        if($Model->where("id=".$_POST["id"])->save($data)){
            $opdata["content"]="修改了引导图信息。编号为:".$_POST["id"]."。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Yindao/edit");
        }else{
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Yindao/edit");
        }
    }


    public function del(){
        $id=$_POST["id"];
        $Model=M("Yindao");
        $where="id=".$id;
        $res=$Model->where($where)->find();
        unlink($res["images"]);
        $res1=$Model->where($where)->delete();
        if($res1){
            admin_log('删除引导图，编号：'.$id);
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }

    }
}