<?php
namespace Ventocart\Catalog\Controller\Extension\Ventocart\Module;
/**
 * Class moreincategory
 *
 * @package Ventocart\Catalog\Controller\Extension\Ventocart\Module
 */
class MoreInCategory extends \Ventocart\System\Engine\Controller
{
    /**
     * @param array $setting
     *
     * @return string
     */
    public function index(array $setting): mixed
    {

        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        if ($product_id == 0) {
            return '';
        }
        $this->load->language('extension/ventocart/module/moreincategory');


        $data['autoplay'] = $setting['autoplay'];

        $data['products'] = [];

        $this->load->model('extension/ventocart/module/moreincategory');
        $this->load->model('tool/image');

        $results = $this->model_extension_ventocart_module_moreincategory->getmoreincategory($setting['limit'], $product_id);
        $this->load->language('product/thumb');
        if ($results) {
            foreach ($results as $result) {
                $poster = '';

                if ($result['image']) {

                    // Video poster creation
                    $fileExtension = strtolower(pathinfo($result['image'], PATHINFO_EXTENSION));

                    if (in_array($fileExtension, ['mp4', 'mkv', 'avi'])) {

                        // Prepare the resized video thumbnail, the URL is the same as the video
                        $image_format = $this->config->get('config_image_filetype');
                        if ($image_format == 'as_uploaded') {
                            $image_format = ".png";  // Default to PNG if 'as_uploaded' is chosen
                        }
                        $image = $result['image'];

                        $poster = substr_replace($result['image'], '.png', strrpos($result['image'], '.'));  // Only modify the $poster variable
                        $poster = $this->model_tool_image->resize(html_entity_decode($poster, ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height']);

                    }
                    $image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height']);



                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                }

                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $price = false;
                }

                if ((float) $result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                $product_data = [
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'date_added' => $result['date_added'],
                    'new' => (strtotime($result['date_added']) >= time() - 604800) ? true : false,
                    'description' => oc_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'poster' => $poster,
                    'setWidth' => $setting['width'],
                    'setHeight' => $setting['height'],
                    'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
                    'rating' => $result['rating'],
                    'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                ];
                $product_data['cart'] = $this->url->link('common/cart.info');

                $product_data['add_to_cart'] = $this->url->link('checkout/cart.add');

                $data['products'][] = $this->load->view('product/quick_thumb', $product_data);

            }




            return $this->load->view('extension/ventocart/module/moreincategory', $data);


        } else {
            return '';
        }
    }
}
