<?php
namespace app\question\model;

use think\Db;
use think\Model;

class Questionlist extends Model
{
    /**
     * 获取问题列表
     * {is_sensitive=0,is_white=0}
     */

    public function ajaxQuestionlist($page)
    {
         return Db::query('select @rowid:=@rowid+1 as rowid,id,
            title,content,weixin,parent_id,create_time,nickname,identity
            from qa_question_list,(select @rowid:=0) as init
                where is_sensitive=0 and is_white=0 and is_audit=0 and parent_id=0
                    and (locate(?,title)>0 or locate(?,content)>0 or locate(?,nickname)>0)
                    and date(create_time)>=date(date_sub(now(),INTERVAL ? DAY))
                    order by id
                    limit ?,?',
             [$page->text,$page->text,$page->text,$page->days,
                 (intval($page->num)-1)*intval($page->size),intval($page->size)]);
    }  


    // public function ajaxQuestionlist($page)
    // {
    //      return Db::query('select @rowid:=@rowid+1 as rowid,id,
    //         case parent_id when 0 then id else parent_id end as pid,
    //         title,content,weixin,parent_id,create_time,nickname,identity
    //         from qa_question_list,(select @rowid:=0) as init
    //             where is_sensitive=0 and is_white=0 and is_audit=0
    //                 and (locate(?,title)>0 or locate(?,content)>0 or locate(?,nickname)>0)
    //                 order by pid,create_time
    //                 limit ?,?',
    //          [$page->text,$page->text,$page->text,
    //              (intval($page->num)-1)*intval($page->size),intval($page->size)]);
    // }  

    /**
     * 获取图片列表
     *
     */
    public function getGallery($question_id)
    {
        return Db::query('select file_name from qa_refence_gallery where question_id=?',[$question_id]);
    }

  
    public function getRefence($question_id)
    {
        return Db::query('select tech_url from qa_refence_gallery where question_id=?',[$question_id]);
    }  

    /**
     * 获取问题列表长度
     *
     */
    public function getQuestionlistTotal($page)
    {
        $result=Db::query("select count(*) as amount from qa_question_list
            where is_sensitive=0 and is_white=0 and (locate(?,title)>0 or locate(?,content)>0 or locate(?,nickname)>0)",
            [$page->text,$page->text,$page->text]);
        
         return reset($result);
    } 

    /**
     * 审核贴子
     *
     */

    public function ajaxIsAudit($id)
    {
        return Db::execute('update qa_question_list set is_audit=true where id=?',[$id]);
    }


    /**
     * 删贴
     *
     */
    public function ajaxRemove($id)
    {
        Db::startTrans();
        try{
            // 设置删除标记
            Db::execute('delete from qa_refence_gallery where question_id=?',[$id]);
            Db::execute('delete from qa_question_list where id=?',[$id]);

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;
    }


}
