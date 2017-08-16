<?php

namespace Lxh\Kernel\Spiders\Bellelily\Parsers;

class Product extends Parser
{
    // 产品id
    public $id;

    public function handler(& $html)
    {
        $dom = $this->dom($html);

        // 获取接口数据
        $content = $this->getApiResult();

        $leftDom = $dom->find('.productLeft', 0);

        if (! is_object($leftDom)) {
            $this->finder->error("解析数据失败，不存在'.productLeft'节点，id：{$this->id}");
            return [];
        }

        $prod['id'] = $this->id;

        // seo信息
        $prod['SEO'] = $this->parseSEO($dom);

        // 产品名称
        $prod['name'] = get_value($content, 'goods_name');

        // 描述
        $prod['desc'] = $this->parseDesc($leftDom);

        // 产品编号
        $prod['no'] = $leftDom->find('.dirModel div', 1)->innertext;

        // 价格
        $prod['price'] = get_value($content, 'shop_price_org');

        // 折扣
        $prod['discount'] = get_value($content, 'promote_price_sale');

        // 图片
        $prod['img'] = $this->parseImg($leftDom);

        // 重量
        $prod['weight'] = get_value($content, 'goods_weight');

        // 高
        $prod['height'] = get_value($content, 'height');

        // 添加时间
        $prod['addtime'] = get_value($content, 'add_time');

        // 分类id
        $prod['cate_id'] = get_value($content, 'cat_id');

        // 分类名称
        $prod['cate_name'] = get_value($content, 'cat_name');

        // 单位
        $prod['package_number'] = get_value($content, 'package_number');

        // 子产品
        $prod['style'] = $this->parseStyle($dom, $leftDom, $content);

        // 属性
        $prod['attrs'] = $this->parseAttrs($prod);

        $prod['is_new'] = get_value($content, 'is_new');

        $prod['is_hot'] = get_value($content, 'is_hot');

        $prod['package_weight'] = get_value($content, 'package_weight');

        // 尺码表
        $prod['argx_size'] = $this->parseArgxSize($dom);

        unset($prod['desc']);

        return $prod;
    }

    // 尺码表
    protected function parseArgxSize($dom)
    {
        $data = [];

        $anotherType = false;

        foreach ($dom->find('.szieTableBox table tr') as $i => & $tr) {
            // 子标题跳过
            if ($tr->class == 'border_bottom') continue;

            $sizeName = '';

            // 标题
            foreach ($tr->find('th') as $k => & $th) {
                if ($k == 0) continue;

                $content = ($p = $th->find('p', 0)) ? $p : $th;

                //标题
                $data['title'][] = trim($content->innertext);
                $anotherType = true;
            }

            foreach ($tr->find('td') as $k => & $td) {
                $content = ($p = $td->find('p', 0)) ? $p : $td->find('span', 0);
                $content = $content ?: $td;

                if ($i == 0 && !$anotherType) {
                    if ($k == 0) continue;
                    //标题
                    $data['title'][] = trim(str_replace('&nbsp;', '', $content->innertext));
//                    break;
                } else {
                    if ($k == 0) {
                        $sizeName = trim(str_replace('&nbsp;', '', $content->innertext));
                        $data[$sizeName] = [];
                    } else {
                        if ($k % 2 == 0) continue;
                        // 只记录厘米
                        $data[$sizeName][] = trim(str_replace('&nbsp;', '', $content->innertext));
                    }
                }

            }
        }

        $data['img_size'] = $this->parseArgxSizeImg($dom);

        return $data;
    }

    // 图片尺码
    protected function parseArgxSizeImg($dom)
    {
        $data = [];
        foreach ($dom->find('.sizeTable .sizeTableTitle') as $k => $title) {
            $data[$k]['title'] = $title->innertext;
            $data[$k]['img'] = $dom->find('.sizeTable img', $k)->src;
        }
        return $data;
    }

    // 属性解析
    protected function parseAttrs(array & $prod)
    {
        $data = [];

        // 非属性
        $notAttrs = [
            'Package', 'Item Weight',
        ];

        foreach ($prod['desc'] as $name => & $value) {
            if (in_array($name, $notAttrs)) continue;

            $data[$name] = trim($value);
        }

        return $data;
    }

    // 子产品
    protected function parseStyle($dom, $leftDom, & $content)
    {
        $ext = unserialize(get_value($content, 'goods_number_ext'));

        $data = [];

        if (count($ext) > 1) {
            $data['is_style'] = 1;

            $data['styles'] = [];

            foreach ($leftDom->find('#ordinary li') as & $li) {
                $title = $li->find('p', 0)->innertext;

                $data['styles'][$title] = [];

                foreach ($li->find('div', 0)->find('span') as $k => & $span) {
                    $data['styles'][$title][$k]['name'] = $span->innertext;
                    $data['styles'][$title][$k]['value'] = $span->namevalue;
                    if ($div = $dom->find('#goods-img-color-' . $span->nameattr, 0)) {
                        $data['styles'][$title][$k]['img'] = str_replace('http://images.bellelily.com/', '', $div->value);
                    }
                }
            }

        } else {
            $data['is_style'] = 0;
        }

        return $data;
    }

    protected function parseImg($leftDom)
    {
        $data = ['images_big' => '', 'img_url' => 'http://images.bellelily.com/'];
        $i = 0;
        foreach ($leftDom->find('#showArea > a') as $img) {
            if ($i == 0) {
                $data['images_small'] = str_replace($data['img_url'], '', $img->rev);
            }

            $data['images_big'] .= '|' . str_replace($data['img_url'], '', $img->href);

            $i++;
        }
        return $data;
    }

    // 获取api抓取的数据
    protected function getApiResult()
    {
        return $this->cache->setType('prod-api-result')->get($this->id);
    }

    // 描述信息
    protected function parseDesc($leftDom)
    {
        $data = [];
        foreach ($leftDom->find('#describe li') as & $li) {
            if (! $span = $li->find('span', 0)) {
                continue;
            }

            $key = $span->innertext;

            $key = trim(str_replace(':', '', $key));

            $data[$key] = $li->find('i', 0)->innertext;
        }
        
        return $data;
    }
}
