<?php

namespace Lexik\Bundle\ColissimoBundle\Service;

use Lexik\Bundle\ColissimoBundle\Exception\FailedRequestException;
use Lexik\Bundle\ColissimoBundle\Exception\InvalidRequestException;

use WSColissimo\WSColiPosteLetterService\ClientInterface;
use WSColissimo\WSColiPosteLetterService\Request\LetterColissimoRequest;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Address;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\AddressDest;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\DestEnv;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\ExpEnv;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Parcel;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\ServiceCallContext;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Letter;
use WSColissimo\WSColiPosteLetterService\Response\ValueObject\ReturnLetter;

use Symfony\Component\Validator\ValidatorInterface;

/**
 * WSColiPosteLetterService
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class WSColiPosteLetterService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param ClientInterface    $client
     * @param ValidatorInterface $validator
     * @param array              $params
     */
    public function __construct(ClientInterface $client, ValidatorInterface $validator, array $params)
    {
        $this->client     = $client;
        $this->validator  = $validator;
        $this->params     = $params;
    }

    /**
     * Get a delivery label for a parcel and a recipient
     *
     * @param array   $parcel
     * @param array   $recipient
     * @param array   $sender
     * @param boolean $validate
     *
     * @throws InvalidRequestException
     * @throws FailedRequestException
     * @throws \SoapFault
     *
     * @return ReturnLetter
     */
    public function getLabel(array $parcel, array $recipient, array $sender = array(), $validate = true)
    {
        $request = $this->buildLetterColissimoRequest($parcel, $recipient, $sender);

        if ($validate) {
            $violations = $this->validator->validate($request->getLetter());

            if ($violations->count() > 0) {
                $exception = new InvalidRequestException('The request is not valid, please check the violations list');
                $exception->setViolations($violations);

                throw $exception;
            }
        }

        $response = $this->client->getLetterColissimo($request);

        if (!$response->isSuccess()) {
            throw new FailedRequestException($response->getErrorMessage());
        }

        return $response->getReturnLetter();
    }

    /**
     * Build the LetterColissimoRequest to pass to the client
     *
     * @param array $parcelData
     * @param array $recipientData
     * @param array $senderData
     *
     * @return \WSColissimo\WSColiPosteLetterService\Request\LetterColissimoRequest
     */
    protected function buildLetterColissimoRequest(array $parcelData, array $recipientData, array $senderData = array())
    {
        $letter = new Letter();

        $letter->setContractNumber($this->params['contract_number']);
        $letter->setPassword($this->params['password']);

        $parcel = $this->getParcelFromArray($parcelData);
        $letter->setParcel($parcel);

        $service = $this->getServiceFromConfig();
        $letter->setService($service);

        $exp = count($senderData) > 0 ? $this->getExpFromArray($senderData) : $this->getExpFromConfig();
        $letter->setExp($exp);

        $dest = $this->getDestFromArray($recipientData);
        $letter->setDest($dest);

        $request = new LetterColissimoRequest();
        $request->setLetter($letter);

        return $request;
    }

    /**
     * @return \WSColissimo\WSColiPosteLetterService\Request\ValueObject\ServiceCallContext
     */
    protected function getServiceFromConfig()
    {
        $service = new ServiceCallContext();

        foreach ($this->params['service_call_context'] as $key => $value) {
            $method = 'set' . ucfirst(self::toCamelCase($key));
            $service->$method($value);
        }

        return $service;
    }

    /**
     * @return \WSColissimo\WSColiPosteLetterService\Request\ValueObject\ServiceCallContext
     */
    protected function getExpFromConfig()
    {
        $address = new Address();

        foreach ($this->params['sender'] as $key => $value) {
            $method = 'set' . ucfirst(self::toCamelCase($key));
            $address->$method($value);
        }

        return new ExpEnv($address);
    }

    /**
     * @param array $data
     *
     * @return \WSColissimo\WSColiPosteLetterService\Request\ValueObject\Parcel
     */
    protected function getParcelFromArray($data)
    {
        $parcel = new Parcel();

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $parcel->$method($value);
        }

        return $parcel;
    }

    /**
     * @param array $data
     *
     * @return \WSColissimo\WSColiPosteLetterService\Request\ValueObject\AddressDest
     */
    protected function getDestFromArray($data)
    {
        $address = new AddressDest();

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $address->$method($value);
        }

        return new DestEnv($address);
    }

    /**
     * @param array $data
     *
     * @return \WSColissimo\WSColiPosteLetterService\Request\ValueObject\AddressDest
     */
    protected function getExpFromArray($data)
    {
        $address = new Address();

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $address->$method($value);
        }

        return new ExpEnv($address);
    }

    /**
     * Transform an underscore-separated string to a camel case string
     *
     * @param string $str
     *
     * @return string
     */
    protected static function toCamelCase($str)
    {
        return preg_replace_callback(
            '/_([a-z0-9])/',
            create_function('$c', 'return strtoupper($c[1]);'),
            $str
        );
    }
}