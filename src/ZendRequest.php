<?php
namespace ZendAdapter;

use Zend\Http\Request;
use Zend\Http\Client;

/**
 * ZendRequest class
 * 
 * Essa classe realiza em formato Fluente assum como o Guzzle
 * a adaptação das requests realizadas com Guzzle para o Zend Http
 * utilziando as PSR'S corretamentes isso fica simples e eficaz.
 * 
 * @package ZendAdapter
 * @author italodeveloper <italoaraujo788@gmail.com>
 * @version 1.0.0
 */
class ZendRequest 
{
    /** @var $guzzle */
    protected $guzzle;
    /** @var $client */
    protected $client;
    public function __construct($guzzle = null)
    {
        if(\is_null($guzzle)){
            $guzzle['base_uri'] = '';
        }
        $this->client = new Client();
        $this->guzzle = $guzzle;
    }  
    
    /**
     * getConfig function
     *
     * Retorna as configurações setadas pelo usuario
     * via injeção de dependencia.
     * 
     * @return array|void
     */
    public function getConfig()
    {
        return $this->guzzle;
    }

    /**
     * request function
     *
     * Simula a request do Guzzle utilizando o Zend Http
     * 
     * @param string $method
     * @param string $url
     * @param [type] $body
     * @return $this
     */
    public function request($method, $url, $body = null, $headers = null)
    {
        if(is_null($headers)){
            $headers = [];
        }
        if(is_null($body)){
            $body = [];
        }
        $method = strtoupper($method);
        if(!is_array($body)){
            return false;
        }
        if(!in_array($method, ['GET', 'POST', 'PUT', 'PATH', 'DELETE'])){
            return false;
        }
        if(isset($body['json'])){
            $body = $body['json'];
        } elseif (isset($body['content'])){
            $body = $body['content'];
        }

        /** Fusão de Headers com configurações do cliente */
        if(isset($this->guzzle['headers']) && is_array($this->guzzle['headers'])){
            $headers = $this->guzzle['headers'] + $headers;
        }

        if($method == 'GET' || $method == 'POST'){
            $this->request = \Zend\Http\ClientStatic::$method(
                $this->guzzle['base_uri'] . $url,
                $body,
                $headers
            );
        } else {
            $request = new Request();
            $request->setUri($this->guzzle['base_uri'] . $url);
            $request->setMethod($method);
            $request->getHeaders()->addHeaders($headers);
            if(isset($body) && !empty($body)){
                $arrayBody = json_decode($body, true);
                if(is_array($arrayBody)){
                    $body = json_encode($arrayBody);
                }
            }
            if(!empty($body)){
                $request->setContent($body);
            }
            $response = $this->client->send($request);
            $this->request  = ['ZendRequest' => [$method => $response->getBody()]];
        }
        return $this;
    }
    /**
     * getBody function
     *
     * Existe para garantir a continuidade e fluidez das chamadas
     * 
     * @return void
     */
    public function getBody()
    {
        return $this;
    }

    /**
     * getContents function
     *
     * Pega todos os dados fluidos realiza as verificações bases,
     * como se a zoop retornou algum error, se sim cria a exception
     *
     * @return string
     * @throws \Exception
     */
    public function getContents()
    {
        $properties = \get_object_vars($this);
        $request = $properties['request'];

        if(is_array($request)){
            $httpNews = ['PUT', 'PATH', 'DELETE'];
            foreach ($httpNews as $requestMethod){
                if(isset($request['ZendRequest']) && !empty($request['ZendRequest'])){
                    if(isset($request['ZendRequest'][$requestMethod])){
                        if(!is_array($request['ZendRequest'][$requestMethod])){
                            $jsonResponse = \json_decode($request['ZendRequest'][$requestMethod], true);
                            if(is_array($jsonResponse) && !empty($jsonResponse)){
                                return \json_encode($jsonResponse);
                            }
                        }
                        return $request['ZendRequest'][$requestMethod];
                    }
                }
            }
        }

        $arrayResponse  = \json_decode($request->getContent(), true);
        if(isset($arrayResponse['error'])){
            throw new \Exception(\json_encode($arrayResponse), 1);
        }
        return \json_encode($arrayResponse);
    }
}