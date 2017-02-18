<?php
namespace app\question\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

//use app\admin\model\User as UserModel;
use app\question\model\Questionlist as QuestionlistModel;
use app\question\model\Whitelist as WhitelistModel;
use app\question\model\Index as IndexModel;
use ecopro\AjaxOutput;
//use ecopro\SubMenu;

class Index extends Controller
{
    private $_whitelist='';
    private $_questionlist='';
    private $_index='';

    public function __construct(QuestionlistModel $questionlistModel,WhitelistModel $whitelistModel,IndexModel $indexModel)
    {
        parent::__construct();
        //$this->_user=$userModel;
        $this->_whitelist=$whitelistModel;
        $this->_questionlist=$questionlistModel;
        $this->_index=$indexModel;
    }



    public function index()
    {

        $form=input('userinfo');
        $userInfo=json_decode($form);

        $userInfo->identity=$this->_index->getUserIdentity($userInfo);
        if(empty($userInfo->identity)){
            $userInfo->identity='访客';
        }

        Session::set('userInfo',$userInfo);

        return view();
    }

    public function ajaxUserInfo()
    {
        $userInfo=Session::pull('userInfo');
        return AjaxOutput::success('',$userInfo);
    }


    public function ajaxFrontQuestionlist()
    {
        $frontQuestionlist=$this->_questionlist->ajaxFrontQuestionlist();
        return AjaxOutput::success('',$frontQuestionlist);
    }

    public function ajaxFrontQuestionChild()
    {
        $form=input('params');
        $question=json_decode($form);


        $result=$this->_questionlist->ajaxFrontQuestionChild($question);
        //$questions=AjaxOutput::toObject($result);
        $questions=[];
        foreach($result as $item){
            $issue=AjaxOutput::toObject($item);
            $issue->images=[];
            $images=$this->_questionlist->ajaxFrontQuestionResource($issue->id);
            foreach($images as $image){
                $imagePath=substr(ROOT_PATH,0,-1).$image['file_name'];
                $issue->images[]=file_get_contents($imagePath);
            }
            $questions[]=$issue;
        }

        return AjaxOutput::success('',$questions);
    }



    public function ajaxFrontBindExpert()
    {
        $form=input('params');
        $expert=json_decode($form);

        $rows=$this->_whitelist->ajaxFrontBindExpert($expert);

        return $rows>0?Json(AjaxOutput::success('专家绑定成功')):Json(AjaxOutput::error('专家绑定失败'));
    }


    /**
     * 发贴 
     *
     * $title,$content,$images[],$headImageurl,$weixin,$identity
     *
     */
    public function ajaxFrontQuestionSubmit()
    {
        

        $form=input('params');
        $question=json_decode($form);

        $insertId=$this->_questionlist-> ajaxFrontQuestionSubmit($question);

        $gallery=new \stdClass;
        $gallery->question_id=$insertId;
        $gallery->file_name='';



        foreach($question->images as $image){
            $code=uniqid();
            $file_path='public'.DS.'uploads'.DS.'question'.DS.date('Ymd');
            $path=substr(ROOT_PATH,0,-1).DS.$file_path;
            
            $gallery->file_name=DS.$file_path.DS.$code;
            $this->_questionlist-> ajaxFrontGallery($gallery);

            if(!is_dir($path){
                mkdir($path);
            }
            
            file_put_contents($path.DS.$code,$image);
        }

        // 查找敏感词
        $sensitive=substr(ROOT_PATH,0,-1).DS."public".DS."uploads".DS."question".DS."sensitive.txt";
        $text=file_get_contents($sensitive);
        $words=mb_split(',',$text);
        foreach($words as $word){
            echo $word."<br/>";
            if(mb_strpos($word,$question->title)!==false || mb_strpos($word,$question->content!==false)){
                // 存在敏感词
                $this->_questionlist-> ajaxFrontSensitive($gallery);
                break;             
            }
        }


        return Json(AjaxOutput::success('发贴成功'));
    }

}
