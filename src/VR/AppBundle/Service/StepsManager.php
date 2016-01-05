<?php

namespace VR\AppBundle\Service;

use VR\AppBundle\Entity\Message;

/**
 * Class StepsManager
 *
 * @package VR\AppBundle\Service
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class StepsManager
{
    protected $parsedJson;

    protected $lastJsonError;

    public function setStepsArray($stepsArray)
    {
        $this->parsedJson = $stepsArray;
    }

    public function parse($input, $stopOnError = false)
    {
        $this->parsedJson = json_decode($input, true);

        if (json_last_error_msg() != 'No error') {
            $this->lastJsonError = json_last_error_msg();
        }

        if ($stopOnError && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('The input string is not a valid JSON.');
        }

        return $this->parsedJson;
    }

    public function getLastParseError()
    {
        return $this->lastJsonError;
    }

    public function getSteps()
    {
        $this->parsedJson;
    }

    public function getCurrentStepNumber()
    {
        if (count($this->parsedJson)) {
            foreach ($this->parsedJson as $stepNumber => $stepData) {
                if ($stepData['GUID'] == null) {
                    return $stepNumber;
                }
            }
        }

        return null;
    }

    public function getNextStepNumber()
    {
        if (count($this->parsedJson)) {
            foreach ($this->parsedJson as $stepNumber => $stepData) {
                if ($stepData['GUID'] == null) {
                    return isset($this->parsedJson['steps'][$stepNumber + 1]) ? $stepNumber + 1 : null;
                }
            }
        }

        return null;
    }


    public function countAllSteps()
    {
        return count($this->parsedJson);
    }

    public function countCompletedSteps()
    {
        $counter = 0;

        if (count($this->parsedJson)) {
            foreach ($this->parsedJson as $step) {
                if (in_array($step['Status'], Message::$completedStatuses)) {
                    $counter++;
                }
            }
        }

        return $counter;
    }

    public function getCompletedStepsPercentage()
    {
        return $this->countCompletedSteps() ? round($this->countCompletedSteps() / $this->countAllSteps() * 100) : 0;
    }

    /**
     * @param array $steps
     *
     * @throws \Exception
     * @return string
     */
    public function getStepToParse($steps)
    {
        krsort($steps);

        $currentStep = null;
        $doneStepsNumber = 0;

        if (count($steps)) {
            foreach ($steps as $stepNumber => $stepData) {
                if ($currentStep === null) {
                    $currentStep = $stepNumber;
                }

                if (
                    !in_array($stepData['Status'], ['Done', 'Rerun', 'Cancelled']) &&
                    $stepNumber <= $currentStep
                ) {
                    $currentStep = $stepNumber;
                }

                if (strtolower($stepData['Status']) == 'done') {
                    $doneStepsNumber++;
                }
            }

            $result = array_merge(['Step' => $currentStep], $steps[$currentStep]);

            if ($doneStepsNumber == count($steps)) {
                $result['MessageStatus'] = 'Finished';
            } else {
                isset($result['Status']) && $result['MessageStatus'] = $this->getMessageStatus($result);
            }
        } else {
            $result = ['Step' => null];
        }

        return $result;
    }

    protected function getMessageStatus($currentStepData)
    {
        $messageStatusMap = [
            'New' => 'New',
            'Done' => 'New',
            'Rerun' => 'New'
        ];

        return isset($messageStatusMap[$currentStepData['Status']])
            ? $messageStatusMap[$currentStepData['Status']]
            : $currentStepData['Status'];
    }
}