<?php

namespace Corbado;

use Corbado\Classes\Apis\EmailLinks;
use Corbado\Classes\Apis\SMSCodes;
use Corbado\Classes\Apis\Validation;
use Corbado\Classes\Apis\WebAuthn;
use Corbado\Classes\Apis\Widget;
use Corbado\Classes\Session;
use Corbado\Classes\SessionV1;
use Corbado\Generated\Api\UserApi;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class SDK
{
    private Configuration $config;
    private ClientInterface $client;
    private ?EmailLinks $emailLinks = null;
    private ?SMSCodes $smsCodes = null;
    private ?WebAuthn $webAuthn = null;
    private ?Validation $validation = null;
    private ?Widget $widget = null;
    private ?UserApi $users = null;
    private ?Session $session = null;

    /**
     * Constructor
     *
     * @param Configuration $config
     * @throws Classes\Exceptions\Configuration
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;

        if ($this->config->getHttpClient() === null) {
            if ($this->config->getProjectID() === '') {
                throw new Classes\Exceptions\Configuration('No project ID set, use Configuration::setProjectID()');
            }

            if ($this->config->getApiSecret() === '') {
                throw new Classes\Exceptions\Configuration('No API secret set, use Configuration::setApiSecret()');
            }

            $this->client = new Client(
                [
                    'base_uri' => $this->config->getBackendAPI(),
                    'http_errors' => false,
                    'auth' => [$this->config->getProjectID(), $this->config->getApiSecret()]
                ]
            );
        } else {
            $this->client = $this->config->getHttpClient();
        }
    }

    /**
     * Returns email links handling
     *
     * @return EmailLinks
     */
    public function emailLinks() : EmailLinks {
        if ($this->emailLinks === null) {
            $this->emailLinks = new EmailLinks($this->client);
        }

        return $this->emailLinks;
    }

    /**
     * Returns SMS codes handling
     *
     * @return SMSCodes
     */
    public function smsCodes() : SMSCodes {
        if ($this->smsCodes === null) {
            $this->smsCodes = new SMSCodes($this->client);
        }

        return $this->smsCodes;
    }

    /**
     * Returns WebAuthn handling
     *
     * @return WebAuthn
     */
    public function webAuthn() : WebAuthn {
        if ($this->webAuthn === null) {
            $this->webAuthn = new WebAuthn($this->client);
        }

        return $this->webAuthn;
    }

    /**
     * Returns validation handling
     *
     * @return Validation
     */
    public function validation() : Validation {
        if ($this->validation === null) {
            $this->validation = new Validation($this->client);
        }

        return $this->validation;
    }

    public function widget() : Widget {
        if ($this->widget === null) {
            $this->widget = new Widget($this->client);
        }

        return $this->widget;
    }

    /**
     * Returns users handling
     *
     * @return UserApi
     */
    public function users() : UserApi {
        if ($this->users === null) {
            $config = new Generated\Configuration();
            $config->setUsername($this->config->getProjectID());
            $config->setPassword($this->config->getApiSecret());
            // @phpstan-ignore-next-line
            $config->setAccessToken(null); // Need to null this out, otherwise it will try to use it

            // @phpstan-ignore-next-line
            $this->users = new UserApi($this->client, $config);
        }

        return $this->users;
    }

    /**
     * Returns session handling
     *
     * @return Session
     * @throws Classes\Exceptions\Assert
     * @throws Classes\Exceptions\Configuration
     * @link https://docs.corbado.com/sessions/overview
     */
    public function session() : Session {
        if ($this->session === null) {
            if ($this->config->getJwksCachePool() === null) {
                throw new Classes\Exceptions\Configuration('No JWKS cache pool set, use Configuration::setJwksCachePool()');
            }

            $this->session = new Session(
                $this->client,
                $this->config->getShortSessionCookieName(),
                $this->config->getFrontendAPI(),
                $this->config->getFrontendAPI() . '/.well-known/jwks',
                $this->config->getJwksCachePool()
            );
        }

        return $this->session;
    }
}
