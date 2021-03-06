<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    protected $instanceClass = ProductOptions::class;

    protected $options = [
        '3385' => [
            'value'             => '3385',
            'label'             => 'label',
            'isEvening'         => true,
            'isSunday'          => true,
            'group'             => 'standard_options',
        ],
        // Pakjegemak Options
        '3534' => [
            'value'             => '3534',
            'label'             => 'label',
            'isExtraEarly'      => false,
            'isSunday'          => false,
            'group'             => 'pakjegemak_options',
        ],
        '3544' => [
            'value'             => '3544',
            'label'             => 'label',
            'isExtraCover'      => true,
            'isExtraEarly'      => true,
            'group'             => 'pakjegemak_options',
        ],
        '3089' => [
            'value'             => '3089',
            'label'             => 'label',
            'isEvening'         => true,
            'isSunday'          => true,
            'group'             => 'standard_options',
        ],
    ];

    protected $groups = [
        'standard_options'   => 'Domestic options',
        'pakjegemak_options' => 'Post Office options',
    ];

    /**
     * Test option Array to not be empty
     */
    public function testToOptionArray()
    {
        $instance = $this->getInstance();
        $options  = $instance->toOptionArray();

        $this->assertNotEmpty($options);
    }

    /**
     * @return array
     */
    public function getProductoptionsProvider()
    {
        return [
            [$this->options, ['isEvening' => true], false, ['3385', '3089']],
            [$this->options, ['isSunday'  => false], false, ['3534']],
            [$this->options, ['isEvening' => true], true, ['3089']]
        ];
    }

    /**
     * @param $options
     * @param $filter
     * @param $checkOnAvailable
     * @param $expected
     *
     * @dataProvider getProductoptionsProvider
     */
    public function testGetProductoptions($options, $filter, $checkOnAvailable, $expected)
    {
        $instance = $this->getInstance();

        $this->setProperty('availableOptions', $options, $instance);

        $result = $instance->getProductoptions($filter, $checkOnAvailable);

        $result = array_map(function ($value) {
            return $value['value'];
        }, $result);

        foreach ($expected as $productCode) {
            $this->assertTrue(in_array($productCode, $result));
        }
    }

    /**
     * @return array
     */
    public function setGroupedOptionsProvider()
    {
        return [
            [$this->options, $this->groups, ['Domestic options', 'Post Office options']]
        ];
    }

    /**
     * @param $options
     * @param $groups
     * @param $expected
     *
     * @dataProvider setGroupedOptionsProvider
     */
    public function testGetGroupedOptions($options, $groups, $expected)
    {
        $instance = $this->getInstance();
        $instance->setGroupedOptions($options, $groups);

        $result = $instance->getGroupedOptions();

        $result = array_map(function ($value) {
            return $value['label'];
        }, $result);

        $countResult   = count($result);
        $countExpected = count($expected);

        $this->assertTrue($countResult == $countExpected);

        foreach ($result as $group) {
            $this->assertTrue(in_array($group, $expected));
        }
    }

}
