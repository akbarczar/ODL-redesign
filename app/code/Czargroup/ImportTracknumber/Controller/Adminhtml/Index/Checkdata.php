<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;

class Checkdata extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_fileCsv;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Constants for CSV column names
     */
    const COL_ORDER_NO = 'ordernumber';
    const COL_CARRIER_CODE = 'courier';
    const COL_TRACKING_NUMBER = 'trackingnumber';

    /**
     * Checkdata constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\File\Csv $fileCsv
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\File\Csv $fileCsv,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resource = $resource;
        $this->fileSystem = $fileSystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->_fileCsv = $fileCsv;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {

            $validColumnNames = [self::COL_ORDER_NO, self::COL_CARRIER_CODE, self::COL_TRACKING_NUMBER];
            $connection = $this
                ->resource
                ->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $tblSalesOrder = $connection->getTableName('sales_order_item');
            try {
                $_fileName = "upload_document1";
                $filesData = $this->getRequest()->getFiles($_fileName);
                if ($filesData['name']) {
                    $filetype = $filesData['type'];
                    $uploader = $this
                        ->fileUploaderFactory
                        ->create(['fileId' => $_fileName]);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(array(
                        'csv'
                    ));
                    $path = $this
                        ->fileSystem
                        ->getDirectoryRead(DirectoryList::ROOT)
                        ->getAbsolutePath('var/tracknum');
                    $result = $uploader->save($path);
                    $upload_document = $uploader->getUploadedFilename();
                }
                $csvfile = $path . $upload_document;
                $data_empty = '';
                $title_validity = '';
                $order_not_found = '';
                if (file_exists($csvfile)) {
                    $data = $this
                        ->_fileCsv
                        ->getData($csvfile);
                    $title_validity = $this->validatetitles($data[0]);
                    if ($title_validity == '') {
                        for ($i = 1; $i < count($data); $i++) {
                            $data_empty .= $this->validatedatanotempty($data[$i], $i + 1, $validColumnNames);
                        }
                    } else {
                        unlink($csvfile);
                    }
                    if ($data_empty == '') {
                        for ($i = 1; $i < count($data); $i++) {
                            $order_not_found .= $this->validateordernumber($data[$i], $i + 1, $validColumnNames);
                        }
                    } else {
                        unlink($csvfile);
                    }
                    if ($order_not_found != '') {
                        unlink($csvfile);
                    }
                }
                $resultJson = $this
                    ->resultJsonFactory
                    ->create();
                $errormessage = $data_empty . $title_validity . $order_not_found;
                if ($errormessage != '') {
                    return $resultJson->setData(['successmsg' => '', 'errormsg' => $errormessage]);
                } else {
                    return $resultJson->setData(['successmsg' => true, 'filetoimport' => $csvfile]);
                }
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }
    }

    /**
     * Validate the column titles of the CSV file to ensure they match the expected names.
     *
     * @param array $titles 
     * @return string 
     */
    protected function validatetitles($titles)
    {
        $invalidmsg = '';
        if ($titles[0] == '') {
            $invalidmsg .= "Column 1 can't be Empty. ";
        } else {
            if ($titles[0] != self::COL_ORDER_NO) {
                $invalidmsg .= "Invalid column name " . $titles[0] . '. ';
            }
        }
        if ($titles[1] == '') {
            $invalidmsg .= " Column 2 can't be Empty. ";
        } else {
            if ($titles[1] != self::COL_CARRIER_CODE) {
                $invalidmsg .= " Invalid column name " . $titles[1] . '. ';
            }
        }
        if ($titles[2] == '') {
            $invalidmsg .= " Column 3 can't be Empty. ";
        } else {
            if ($titles[2] != self::COL_TRACKING_NUMBER) {
                $invalidmsg .= " Invalid column name " . $titles[2] . '. ';
            }
        }
        return $invalidmsg;
    }

    /**
     * Check that the data in the CSV file is not empty.
     *
     * @param array $rowdata 
     * @param int $rownum 
     * @param array $validColumnNames 
     * @return string 
     */
    protected function validatedatanotempty($rowdata, $rownum, $validColumnNames)
    {
        $value_empty = '';
        for ($j = 0; $j < count($rowdata); $j++) {
            if ($rowdata[$j] == '') {
                $value_empty .= ' Value at row ' . $rownum . ' for ' . $validColumnNames[$j] . ' can not be EMPTY. ';
            }
        }
        return $value_empty;
    }

    /**
     * Validate that the order number exists in the database.
     *
     * @param array $rowdata 
     * @param int $rownum 
     * @param array $validColumnNames 
     * @return string 
     */
    protected function validateordernumber($rowdata, $rownum, $validColumnNames)
    {
        $order_not_exist = '';
        $connection = $this
            ->resource
            ->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrder = $connection->getTableName('sales_order');
        $incr_id = $rowdata[0];
        $orderid = $connection->fetchOne("SELECT entity_id FROM $tblSalesOrder WHERE increment_id = $incr_id");
        if ($orderid == '') {
            $order_not_exist .= ' Invalid Order number ' . $incr_id . ' at row ' . $rownum . '. ';
        }
        return $order_not_exist;
    }
}
