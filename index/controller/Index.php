<?php
namespace app\index\controller;

use think\Controller;
use think\Exception;
use think\Request;
class Index extends Controller
{    
    public function index()
    {  
       // dump(get_column('user_member'));exit;
        if($this->request->isPost()){
                $module = $this->request->request('module');
                $re = $this->buildModuleDir($module);
               // $re = new \ReflectionClass('app\index\controller\Index');
               // dump($re->getMethods(\ReflectionMethod::IS_PRIVATE));
               if($re){
                   $this->success('添加模块成功');
               }else{
                   $this->error('添加模块失败');
               }
        }else{
            return $this->fetch('index');
        }

    }
    public function file()
    {
        if($this->request->isPost()){
            $dir = $this->request->request('dir');
            $filename = $this->request->request('filename');
            $re = $this->buildFile($dir,$filename);
            if($re){
                $this->success('添加文件成功');
            }else{
                $this->error('添加文件失败');
            }
        }else{
            return $this->fetch('file');
        }
    
    }
    /*
     * 生成模块目录
     */
    private function buildModuleDir($module){
   
        // 定义demo模块的自动生成 （按照实际定义的文件名生成）
       $build = [ $module     => [
                        '__file__'   => ['common.php'],
                        '__dir__'    => ['controller', 'model','validate','view'],
                        'controller' => ['Index', 'User'],
                        'model'      => ['User'],
                        'validate'      => ['User'],
                        'view'       => ['index/index'],
                                ],
                ];
       try {
           \think\Build::run($build,config('app_namespace'),false);  //false类名没有后缀       
           session('module',$module);
           if(is_dir(APP_PATH.$module)){
               return true;
           }else{
               return false;
           }
       } catch (Exception $e) {
           $this->error($e->getMessage());
       }
       
    }
    /*
     * 生成文件
     */
    public function buildFile($dir,$filename){

        if(empty(session('module'))){
            $this->error('请先建立模块'.session('module'));;
        }
        if(empty($dir)){
            $this->error('目录不可为空');;
        }
        if(!is_dir(APP_PATH.session('module').'/')){
             $this->error('请先建立模块'.session('module'));;
        }
        if(!is_dir(APP_PATH.session('module').'/'.$dir.'/')){
            $this->error('请先建立模块'.session('module').'下的'.$dir.'目录');;
        }
        $file = APP_PATH.session('module').'/'.$dir.'/'.ucfirst($filename).EXT;
        
        $space = config('app_namespace'). '\\' . session('module'). '\\' . $dir;
        $class = ucfirst($filename);
        switch ($dir) {
            case 'controller': // 控制器
                $content = "<?php\nnamespace {$space};\n\nuse think\Controller;\nclass {$class} extends Controller\n{\n\n}";
                break;
            case 'model': // 模型
                $content = "<?php\nnamespace {$space};\n\nuse think\Model;\n\nclass {$class} extends Model\n{\n\n}";
                break;
            case 'view': // 视图
                $file = APP_PATH.session('module').'/'.$dir .'/'.$filename . '/index.html';
                if (!is_dir(dirname($file))) {
                    // 创建目录
                    mkdir(dirname($file), 0755, true);
                }
                $content = '';
                break;
            default:
                // 其他文件
                $content = "<?php\nnamespace {$space};\n\nclass {$class}\n{\n\n}";
        }
        
        if (!is_file($file)) {
            file_put_contents($file, $content);
        }
        return true;
    }
    /*
     * 添加表
     */
    public function addtable(){
        $this->assign('tables',get_table());
        return $this->fetch('addtable');
    }
    /*
     * 添加验证
     */
    public function addvalidate(){
        if($this->request->isGet()){
            session('table',$this->request->get('table'));
        }
        // dump(get_column(session('table')));exit;
        $this->assign('col_name',get_column(session('table')));
        return $this->fetch('validate2');
    }
    /*
     * 添加验证,生成代码
     */
    public function addvalidate2(){
       
        $vals=input('post.');
        $cls = get_column(session('table'));
        
        $rs = [];
        $ms = [];
        
        for($k=0;$k<count($cls);$k++){
            $c = $cls[$k]['Field'];
            if(isset($vals[$c.'_'.'require'])){
                if($vals[$c.'_'.'require']=='on'){
                    $rs[$c][]='require';
                    $ms[$c.'.require']=$this->_getf($cls[$k]).'必填';
                }
            }
            if(isset($vals[$c.'_'.'number'])){
                if($vals[$c.'_'.'number']=='on'){
                    $rs[$c][]='number';
                    $ms[$c.'.number']=$this->_getf($cls[$k]).'为数值';
                }
            }
            if(isset($vals[$c.'_'.'float'])){
                if($vals[$c.'_'.'float']=='on'){
                    $rs[$c][]='float';
                    $ms[$c.'.float']=$this->_getf($cls[$k]).'为小数';
                }
            }
            if(isset($vals[$c.'_'.'boolean'])){
                if($vals[$c.'_'.'boolean']=='on'){
                    $rs[$c][]='boolean';
                    $ms[$c.'.boolean']=$this->_getf($cls[$k]).'为布尔';
                }
            }
            if(isset($vals[$c.'_'.'email'])){
                if($vals[$c.'_'.'email']=='on'){
                    $rs[$c][]='email';
                    $ms[$c.'.email']=$this->_getf($cls[$k]).'为EMAIL';
                }
            }
            if(isset($vals[$c.'_'.'accepted'])){
                if($vals[$c.'_'.'accepted']=='on'){
                    $rs[$c][]='accepted';
                    $ms[$c.'.accepted']=$this->_getf($cls[$k]).'为yes/on';
                }
            }
            if(isset($vals[$c.'_'.'date'])){
                if($vals[$c.'_'.'date']=='on'){
                    $rs[$c][]='date';
                    $ms[$c.'.date']=$this->_getf($cls[$k]).'为日期';
                }
            }
            if(isset($vals[$c.'_'.'alpha'])){
                if($vals[$c.'_'.'alpha']=='on'){
                    $rs[$c][]='alpha';
                    $ms[$c.'.alpha']=$this->_getf($cls[$k]).'为字母';
                }
            }
            if(isset($vals[$c.'_'.'array'])){
                if($vals[$c.'_'.'array']=='on'){
                    $rs[$c][]='array';
                    $ms[$c.'.array']=$this->_getf($cls[$k]).'为数组';
                }
            }
            if(isset($vals[$c.'_'.'alphaNum'])){
                if($vals[$c.'_'.'alphaNum']=='on'){
                    $rs[$c][]='alphaNum';
                    $ms[$c.'.alphaNum']=$this->_getf($cls[$k]).'为字母数字';
                }
            }
            if(isset($vals[$c.'_'.'alphaDash'])){
                if($vals[$c.'_'.'alphaDash']=='on'){
                    $rs[$c][]='alphaDash';
                    $ms[$c.'.alphaDash']=$this->_getf($cls[$k]).'为字母数字—_';
                }
            }
            if(isset($vals[$c.'_'.'activeUrl'])){
                if($vals[$c.'_'.'activeUrl']=='on'){
                    $rs[$c][]='activeUrl';
                    $ms[$c.'.activeUrl']=$this->_getf($cls[$k]).'为域名/IP';
                }
            }
            if(isset($vals[$c.'_'.'url'])){
                if($vals[$c.'_'.'url']=='on'){
                    $rs[$c][]='url';
                    $ms[$c.'.url']=$this->_getf($cls[$k]).'为URL';
                }
            }
            if(isset($vals[$c.'_'.'ip'])){
                if($vals[$c.'_'.'ip']=='on'){
                    $rs[$c][]='ip';
                    $ms[$c.'.ip']=$this->_getf($cls[$k]).'为ip';
                }
            }
        
            if(isset($vals[$c.'_'.'regex'])){
                if($vals[$c.'_'.'regex']!=''){
                    $rs[$c][]='regex:'.$vals[$c.'_'.'regex'];
                    $ms[$c.'.regex']=$this->_getf($cls[$k]).'无法通过验证';
                }
            }
        
            if(isset($vals[$c.'_'.'confirm'])){
                if($vals[$c.'_'.'confirm']!=''){
                    $rs[$c][]='confirm:'.$vals[$c.'_'.'confirm'];
                    $ms[$c.'.confirm']=$this->_getf($cls[$k]).'和'.$vals[$c.'_'.'confirm'].'值相同';
                }
            }
            if(isset($vals[$c.'_'.'max'])){
                if($vals[$c.'_'.'max']!=''){
                    $rs[$c][]='max:'.$vals[$c.'_'.'max'];
                    $ms[$c.'.max']=$this->_getf($cls[$k]).'最大值为'.$vals[$c.'_'.'max'];
                }
            }
            if(isset($vals[$c.'_'.'min'])){
                if($vals[$c.'_'.'min']!=''){
                    $rs[$c][]='min:'.$vals[$c.'_'.'min'];
                    $ms[$c.'.min']=$this->_getf($cls[$k]).'最小值为'.$vals[$c.'_'.'min'];
                }
            }
            if(isset($vals[$c.'_'.'before'])){
                if($vals[$c.'_'.'before']!=''){
                    $rs[$c][]='before:'.$vals[$c.'_'.'before'];
                    $ms[$c.'.before']=$this->_getf($cls[$k]).'必须在'.$vals[$c.'_'.'before'].'之前';
                }
            }
            if(isset($vals[$c.'_'.'after'])){
                if($vals[$c.'_'.'after']!=''){
                    $rs[$c][]='after:'.$vals[$c.'_'.'after'];
                    $ms[$c.'.after']=$this->_getf($cls[$k]).'必须在'.$vals[$c.'_'.'before'].'之后';
                }
            }
        }
        $this->assign('table',table_to_class(session('table')));
        $this->assign('cls',$cls);
        $this->assign('rs',$rs);
        $this->assign('ms',$ms);
        return $this->fetch('validate3');
    }
    private function _getf($c){
        if($c['Comment']!=''){
            return $c['Comment'];
        }else
            return $c['Field'];
    }
    /*
     * 生成验证文件
     */
    public function buildvalidatefile($content){
        $content = $this->request->post('content',false);
        if(empty(session('module'))){
            $this->error('请先建立模块'.session('module'));;
        }
        if(!is_dir(APP_PATH.session('module').'/')){
            $this->error('请先建立模块'.session('module'));;
        }
        if(!is_dir(APP_PATH.session('module').'/validate/')){
            $this->error('请先建立模块'.session('module').'下的'.$dir.'目录');;
        }
        $file = APP_PATH.session('module').'/validate/'.table_to_class(session('table')).EXT;
   
        $content = "<?php\n".$content;
    
        if (!is_file($file)) {
            file_put_contents($file, $content);
        }
       return '生成验证文件成功';
    }

}
