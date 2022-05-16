<?php
namespace Hamtaraw\Component;

/**
 * Modal middleware.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractModal extends AbstractComponent
{
    /**
     * Returns the url id.
     *
     * @return string
     */
    public function getUrlId()
    {
        return '';
    }

    /**
     * @inheritdoc
     * @see AbstractComponent::getTemplate()
     */
    public function getTemplate()
    {
        return 'modal.twig';
    }

    /**
     * Returns the modale title.
     *
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Returns true if the modale is closable.
     *
     * @return bool
     */
    public function isClosable()
    {
        return true;
    }

    /**
     * Returns true if the modale has header.
     *
     * @return bool
     */
    public function hasHeader()
    {
        return true;
    }

    /**
     * @inheritdoc
     * @see AbstractComponent::process()
     */
    public function process()
    {
        $this->aView['ComponentModal'] = $this;

        $this->Wrapper->addAttrs([
            'class' => 'hamtaro-modal modal fade',
            'data-modal-urlid' => $this->getUrlId(),
            'data-uipath' => $this->getUiPath(),
        ]);

        $this->Modules->Response()->getSuccess('Nickel')->setExtraParam('modal', $this);
    }
}