<?php
namespace Inspirio\Deployer\Security;


use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class PassPhraseSecurity implements SecurityModuleInterface, ConfigAware
{
    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function authorize(Request $request)
    {
        $session = $request->getSession();

        if ($request->request->has('security_phrase')) {
            $phrase = $request->request->get('security_phrase');
        } elseif ($session->has('security_phrase')) {
            $phrase = $session->get('security_phrase');
        } else {
            $phrase = null;
        }

        $valid = $this->validatePassPhrase($phrase);

        if ($valid) {
            $session->set('security_phrase', $phrase);

            if ($request->request->has('security_phrase')) {
                return new RedirectResponse($request->getUri());
            }

            return true;
        }

        $session->remove('security_phrase');

        if ($phrase !== null) {
            $error = 'Invalid pass-phrase.';
        }

        ob_start();
        include __DIR__ . '/view/passPhraseScreen.html.php';
        $content = ob_get_clean();

        return new Response($content, 401);
    }

    /**
     * Validates pass-phrase.
     *
     * @param string $phrase
     * @return bool
     */
    abstract protected function validatePassPhrase($phrase);
}
