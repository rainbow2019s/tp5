<?php
namespace app\admin\controller;

use think\Controller;
use think\Url;
use think\Session;
use think\Db;

use stdClass;

use Endroid\QrCode\QrCode;
//use tFPDF\PDF;

use tfpdf\Pdf;
use phpexcel\PHPExcel;
use phpexcel\phpexcel\PHPExcel_IOFactory;


use ecopro\Verification;
use ecopro\Weixin;


use app\admin\model\User as UserModel;

class User extends Controller
{
    private $_user;

    public function __construct(UserModel $userModel)
    {
        parent::__construct();

        $this->_user=$userModel;
    }

    
    // 获取所有有效的管理员
    public function getAllEnabled()
    {
        return Json($this->_user->getAllEnabled());
    }

    // 获取所有无效的管理员
    public function getAllDisabled()
    {
        return Json($this->_user->getAllDisabled());
    }

    // 获取所有管理员（有效和无效）
    public function getAll()
    {
        return Json($this->_user->getAll());     
        
    }


    // 创建管理员
    public function add($params='')
    {
        //TODO: 调试时打开
        //$admin=json_decode($params);
        $admin = new stdClass;
        // $admin->name='l.hao';
        // $admin->phone='18015826672';
        // $admin->email='l.hao.2012@qq.com';
        // $admin->is_super=1;
        // $admin->token=uniqid();
        // $admin->password=md5('123456'.$admin->token);

        $admin->name='t.hao';
        $admin->phone='13916242167';
        $admin->email='t.hao.2010@qq.com';
        $admin->is_super=0;
        $admin->token=uniqid();
        $admin->password=md5('123456'.$admin->token);

        $rows=$this->_user->add($admin);
        return Json($rows);

    }

    // 普通管理员绑定应用功能
    public function bind($params='')
    {
        $status=$this->_user->bind(1,[1,2,3]);
        dump($status);
    }

    // 普通管理员解绑应用功能
    public function unbind($params='')
    {
        $status=$this->_user->unbind(1,[2,3]);
        dump($status);

    }

    public function adminBindActivities($params='')
    {
        return Json($this->_user->adminBindActivities(1));
    }


    //-------------------------------------------------------------
    // 以下为测试代码
    //-------------------------------------------------------------

    public function getWeixinAccessToken()
    {
        $weixin=new Weixin(NULL);
    
        //return $weixin->getAccessTokenURL();
        $weixin->getAccessToken();
        return $weixin->setCustomMenu(NULL);
    }

    public function tt()
    {
        return Verification::generate();
    }

    public function index()
    {
        //echo $id;
        //$this->error('我也不知道'); # 默认返回前一页

        $this->assign('value', 'World!');
        return view();

        //return $this->fetch();
    }

    public function appTest()
    {

        $arr=['a','b','c'];
        $arr2=[['A2','B2','C2'],['A3','B3','C3']];

        $this->assign('arr',$arr);
        $this->assign('arr2',$arr2);

        $obj=new stdClass;
        $obj->id=23;
        $obj->name='bbb';

        $this->assign('obj',$obj);

        return view();
    }


    public function qrCode()
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText('Life is too short to be generating QR codes')
            ->setSize(100)
            ->setPadding(2)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            //->setLabel('Scan the code')
            //->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
            // now we can directly output the qrcode
            header('Content-Type: '.$qrCode->getContentType());
            $qrCode->render();
    }

    public function pdfTest()
    {

        //$pdfLibrary = new PDF();

        $pdfLibrary = new Pdf();
        $pdfLibrary->AddPage();
 
        $pdfLibrary->image('http://img10.360buyimg.com/n0/g14/M02/00/05/rBEhVVKBuHAIAAAAAAHGc743SCcAAFhpwP9GxsAAcaL931.jpg',
             10,10,30,46,'JPG');
 
        // Add a Unicode font (uses UTF-8)
        $pdfLibrary->AddFont('Yahei','','Monaco_Yahei.ttf',true);
        $pdfLibrary->SetFont('Yahei','',8);
 
        // Load a UTF-8 string from a file and print it
        // //$txt = file_get_contents('HelloWorld.txt');
        $pdfLibrary->Write(8, "中文测试");

        $pdfLibrary->Output('abc.pdf','D');

    }

    public function phpExcelTest()
    {
        //require ROOT_PATH."vendor".DS."tanghao2018".DS."phpexcel".DS."src".DS."PHPExcel.php";

        $objPHPExcel = new PHPExcel();
 
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                 ->setLastModifiedBy("Maarten Balliauw")
                 ->setTitle("Office 2007 XLSX Test Document")
                 ->setSubject("Office 2007 XLSX Test Document")
                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                 ->setKeywords("office 2007 openxml php")
                 ->setCategory("Test result file");
 
 
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Hello')
                ->setCellValue('B2', 'world!')
                ->setCellValue('C1', 'Hello')
                ->setCellValue('D2', 'world!');
 
        // Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A4', 'Miscellaneous glyphs')
                 ->setCellValue('A5', '中国人');
 
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
 
 
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
 
 
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
 
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
 
        //echo "excel2007";
        $objWriter = PHPExcel_IOFactory::createWriter2007($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }


    public function get()
    {
        // echo $this->request->domain();
        // echo "<br/>";
        // echo $this->request->baseFile();
        // echo "<br/>";
        // echo $this->request->url();
        // echo "<br/>";
        // echo $this->request->url(true);
        // echo "<br/>";
        // echo $this->request->baseUrl();
        // echo "<br/>";
        // $url=Url::build('abc');
        // return xml(['url'=>Url::build('abc')]);

        $name=input('name');
        if($name=='999'){
            //$this->success('dispatch','abc');
            $this->redirect('abc');
        }
    }

    public function do_ajax_get($module,$service,$action,$params)
    {
        return "module:{$module} service:{$service} action:{$action} params:{$params}";
    }


    public function do_ajax_post($module,$service,$action,$params)
    {
        $this->redirect("do_ajax_get?moudle={$module}&service={$service}&action={$action}&params={$params}");
    }

    public function test($id)
    {
        echo $id;
        $this->redirect('go', ['id' => $id, 'id2' => 444]);
    }

    public function go($id, $id2)
    {
        echo $id . ',' . $id2;
    }

    public function abc()
    {
        echo $this->request->url();

        $user=new \stdClass;
        $user->id=23;
        $user->name='刘好';

        $this->request->bind('user',$user);

        echo "<br/>";
        echo $this->request->user->id;

    }

    public function bbc()
    {
        //$result=Db::query('select * from trd_customers where code=:code',['code'=>'SDFL']);
        //return json($result);
        //$list=null;
        $list= Db::table('trd_customers')->select();
        $this->assign('list',$list);

        //echo json_encode($list);

        return view();
    }

    public function magicConst()
    {
        echo __FILE__;
        echo __DIR__;
        echo __LINE__;
    }

    public function urlParams($name,$city)
    {
        echo $name.','.$city;
        echo Url::build('urlparams',['name'=>'lh2016','city'=>'shanghai']);

        //Session::set('name','l.hao');
        //echo Session::get('name');
        //echo Session::pull('name');
        echo "<br/>-----------------------------<br/>";
        if(!Session::has('name')){
            Session::set('name','l.hao.2012@qq.com');
        }
        
        echo Session::get('name');
    }
}
