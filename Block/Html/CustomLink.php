<?php
/**
 * GiaPhuGroup Co., Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GiaPhuGroup.com license that is
 * available through the world-wide-web at this URL:
 * https://www.giaphugroup.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    PHPCuong
 * @package     PHPCuong_TopMenu
 * @copyright   Copyright (c) 2019-2020 GiaPhuGroup Co., Ltd. All rights reserved. (http://www.giaphugroup.com/)
 * @license     https://www.giaphugroup.com/LICENSE.txt
 */

namespace PHPCuong\TopMenu\Block\Html;

use \Magento\Framework\App\ObjectManager;

class CustomLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getCurrent() || $this->getUrl($this->getPath()) == $this->getUrl($this->getMca());
    }

    /**
     * Get current mca
     *
     * @return string
     */
    private function getMca()
    {
        // Checking if the current page is a home page
        $currentFullAction = $this->getRequest()->getFullActionName();
        if ($currentFullAction == 'cms_index_index') {
            return '/';
        }

        // checking if the current page is a CMS page
        if ($currentFullAction == 'cms_page_view') {
            $currentPageId = (int)$this->getRequest()->getParam('id');
            /** @var \Magento\Cms\Model\Page $page */
            $page = $this->getPageModel()->load($currentPageId);
            $currentCmsIdentifier = $page->getIdentifier();
            if ($this->getPath() == $currentCmsIdentifier) {
                return $this->getPath();
            }
        }

        $routeParts = [
            'module' => $this->getRequest()->getModuleName(),
            'controller' => $this->getRequest()->getControllerName(),
            'action' => $this->getRequest()->getActionName(),
        ];

        $parts = [];
        foreach ($routeParts as $key => $value) {
            if (!empty($value) && $value != $this->_defaultPath->getPart($key)) {
                $parts[] = $value;
            }
        }
        return implode('/', $parts);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted() || $this->isCurrent()) {
            $highlight = ' active';
        }

        $html = '<li class="level0 level-top ui-menu-item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
        $html .= $this->getTitle()
            ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
            : '';
        $html .= $this->getAttributesHtml() . '>';

        $html .= '<span>'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())).'</span>';

        $html .= '</a></li>';

        return $html;
    }

    /**
     * Generate attributes' HTML code
     *
     * @return string
     */
    private function getAttributesHtml()
    {
        $attributesHtml = '';
        $attributes = $this->getAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute => $value) {
                $attributesHtml .= ' ' . $attribute . '="' . $this->escapeHtml($value) . '"';
            }
        }

        return $attributesHtml;
    }

    /**
     * CMS Page Model
     *
     * @return \Magento\Cms\Model\Page
     */
    private function getPageModel()
    {
        if ($this->_cmsPage === null) {
            $this->_cmsPage = ObjectManager::getInstance()->get(\Magento\Cms\Model\Page::class);
        }
        return $this->_cmsPage;
    }
}
