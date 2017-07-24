<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/30
 * Time: 23:28
 */

namespace Lxh\Admin\Controller;

use Lxh\Http\Request;
use Lxh\Http\Response;

class Language extends Controller
{
    /**
     * 获取语言包数据接口
     *
     * @param Request $req
     * @param Response $resp
     * @return array
     */
    public function actionGet(Request $req, Response $resp)
    {
        $scopes = explode(',', I('scopes'));

        if (empty($scopes)) {
            return $this->success('sucess', ['list' => []]);
        }

        $lang = I('lang', 'en');

        return $this->success('sucess', ['list' => language()->getPackages($scopes, $lang)]);
    }

    public function actionList()
    {
        $file = file_manager();

        $languagePackDir = language()->getBasePath();

        $fileList = $file->getFileList($languagePackDir, true);
//debug($fileList);

        assign('list', $fileList);

        return fetch_complete_view('List');
    }

    /**
     * 获取语言包接口
     */
    public function actionGetPackage()
    {
        $path = I('content');

        if (empty($path)) {
            return $this->error();
        }
        $languagePackDir = language()->getBasePath();

//        print_r($languagePackDir . ltrim($path, '/'));

        $list = file_manager()->getPhpContents($languagePackDir . $path);

        return $this->success('success', ['content' => & $list]);

    }

    /**
     * 保存语言包接口
     */
    public function actionSave()
    {
        if (empty($_POST['path']) || empty($_POST['content'])) {
            return $this->error();
        }

        $_POST['content'] = json_decode($_POST['content'], true);

        $languagePackDir = language()->getBasePath();

        $result = file_manager()->putPhpContents($languagePackDir . $_POST['path'], $_POST['content']);

        if ($result) {
            return $this->success();
        }
        return $this->error();
    }

    /**
     * 创建一个新的分类
     */
    public function actionCreateCategory()
    {
        if (empty($_POST['path']) || empty($_POST['name'])) {
            return $this->error();
        }
        $file = file_manager();
        $languagePackDir = language()->getBasePath();

        $path = $languagePackDir . $_POST['path'];

        $package = $file->getPhpContents($path);

        if (isset($package[$_POST['name']])) {
            return $this->error("The category already exists");
        }

        $package[$_POST['name']] = [];

        if ($file->putPhpContents($path, $package)) {
            return $this->success('success', ['content' => & $package]);
        }

        return $this->failed();
    }

    /**
     * 创建语言包文件接口
     */
    public function actionCreateFile()
    {
        $lang = I('lang');
        $module = I('module');
        $file = ucfirst(I('file'));

        if (! $lang || ! $module || ! $file) {
            return $this->error();
        }
        if (strpos($file, '.') !== false) {
            return $this->error();
        }

        $languagePackDir = language()->getBasePath();
        $path = "{$languagePackDir}{$lang}/{$module}/{$file}.php";

        if (is_file($path)) {
            return $this->error('File already exists');
        }

        $data = [
            'labels' => [],
            'fields' => [],
        ];

        if (file_manager()->putPhpContents($path, $data)) {
            return $this->success();
        }
        return $this->failed();
    }

    /**
     * 创建分类下的key - value键值对接口
     *
     */
    public function actionCreateValue()
    {
        if (empty($_POST['path']) || empty($_POST['content'])) {
            return $this->error();
        }
        $path = $_POST['path'];
        $content = $_POST['content'];

        $language = language();
        $file     = file_manager();

        $path = $language->getBasePath() . $path;

        if ($file->mergePhpContents($path, $content)) {
            return $this->success('Success', ['content' => $file->getPhpContents($path)]);
        }

        return $this->failed();
    }

    /**
     * 创建options下的key - value键值对接口
     *
     */
    public function actionCreateOptions()
    {
        return $_POST;
    }
}
