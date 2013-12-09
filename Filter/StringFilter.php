<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Filter;

use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class StringFilter extends Filter
{
    /**
     * @param ProxyQueryInterface $query
     * @param string              $name
     * @param string              $field
     * @param string              $data
     * @internal param string $alias
     * @return void
     */
    public function filter(ProxyQueryInterface $query, $name, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        $data['value'] = trim($data['value']);

        if (strlen($data['value']) == 0) {
            return;
        }

        $data['type'] = isset($data['type']) && !empty($data['type']) ? $data['type'] : ChoiceType::TYPE_CONTAINS;

        if ($data['type'] == ChoiceType::TYPE_EQUAL) {
            $query->setKeys([
                $query->getDocumentType(),
                $field,
                $data['value'],
            ]);
        } elseif ($data['type'] == ChoiceType::TYPE_CONTAINS) {
            $query->setStartKey([
                $query->getDocumentType(),
                $field,
                $data['value'],
            ]);

            $query->setEndKey([
                $query->getDocumentType(),
                $field,
                $data['value']."\\xFFF0",
            ]);
        } elseif ($data['type'] == ChoiceType::TYPE_NOT_CONTAINS) {
        }

        $this->active = true;
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return array();
    }

    public function getRenderSettings()
    {
        return array('sonata_type_filter_choice', array(
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'label' => $this->getLabel()
        ));
    }

}
