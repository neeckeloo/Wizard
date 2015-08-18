<?php
namespace Wizard;

use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

class WizardProcessor
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var Wizard
     */
    protected $wizard;

    /**
     * @param HttpRequest $request
     * @param HttpResponse $response
     */
    public function __construct(HttpRequest $request, HttpResponse $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * @param  Wizard $wizard
     * @return self
     */
    public function setWizard(Wizard $wizard)
    {
        $this->wizard = $wizard;
        return $this;
    }

    public function process()
    {
        if (!$this->wizard || !$this->request->isPost()) {
            return;
        }

        $post   = $this->request->getPost();
        $values = $post->getArrayCopy();
        if (isset($values['previous'])) {
            $this->wizard->previousStep();
            return;
        }

        if (isset($values['cancel'])) {
            return $this->doCancel();
        }

        $this->processCurrentStep($values);

        $steps       = $this->wizard->getSteps();
        $currentStep = $this->wizard->getCurrentStep();

        if (!$currentStep->isComplete()) {
            return;
        }

        if ($currentStep->isComplete() && $steps->isLast($currentStep)) {
            return $this->completeWizard();
        }

        $this->wizard->nextStep();
    }

    /**
     * @param array $values
     */
    protected function processCurrentStep(array $values)
    {
        $currentStep = $this->wizard->getCurrentStep();

        $wizardEventManager = $this->wizard->getEventManager();
        $wizardEventManager->trigger(WizardEvent::EVENT_PRE_PROCESS_STEP, $currentStep, [
            'values' => $values,
        ]);

        $complete = $currentStep->process($values);
        if (null !== $complete) {
            $currentStep->setComplete($complete);
        }
        $currentStep->setData($values);

        $wizardEventManager->trigger(WizardEvent::EVENT_POST_PROCESS_STEP, $currentStep);
    }

    /**
     * @return HttpResponse|null
     */
    protected function completeWizard()
    {
        $wizardEvent = new WizardEvent();
        $wizardEvent->setWizard($this->wizard);

        $wizardEventManager = $this->wizard->getEventManager();
        $wizardEventManager->trigger(WizardEvent::EVENT_COMPLETE, $wizardEvent);

        return $this->doRedirect();
    }

    /**
     * @return HttpResponse
     */
    protected function doRedirect()
    {
        $options     = $this->wizard->getOptions();
        $redirectUrl = $options->getRedirectUrl();

        return $this->redirect($redirectUrl);
    }

    /**
     * @return HttpResponse
     */
    protected function doCancel()
    {
        $options   = $this->wizard->getOptions();
        $cancelUrl = $options->getCancelUrl();

        return $this->redirect($cancelUrl);
    }

    /**
     * @param  string $url
     * @return HttpResponse
     */
    protected function redirect($url)
    {
        $this->response->getHeaders()->addHeaderLine('Location', $url);
        $this->response->setStatusCode(302);

        return $this->response;
    }
}
