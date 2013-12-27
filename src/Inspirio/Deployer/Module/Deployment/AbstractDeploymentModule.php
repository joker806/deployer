<?php
namespace Inspirio\Deployer\Module\Deployment;

use Inspirio\Deployer\Module\AbstractModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Base action implementation.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
abstract class AbstractDeploymentModule extends AbstractModule implements DeploymentModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        $sectionName = $request->query->get('section');

        return $this->renderSection($sectionName);
    }

    /**
     * List of module sections.
     *
     * @return array
     */
    abstract protected function getSections();

    /**
     * Renders module section.
     *
     * @param Request $request
     * @param string|null $sectionName
     * @return Response
     *
     * @throws \LogicException
     */
    private function renderSection(Request $request, $sectionName = null)
    {
        $sections = $this->getSections();

        if ($sectionName === null) {
            $sectionName = reset(array_keys($sections));
        }

        if (!isset($sections[$sectionName])) {
            return new Response('404 Not Found', 404);
        }

        $sectionRenderer = 'render' . ucfirst($sectionName);

        if (!method_exists($this, $sectionRenderer)) {
            throw new \LogicException("Missing section '{$sectionName}' renderer in '{$this->getName()}' module");
        }

        $sectionData = call_user_func(array($this, $sectionRenderer), $request);

        // returned string - custom rendered section
        if (is_string($sectionData)) {
            return $sectionData;
        }

        if ($sectionData === true) {
            $sectionData = array();
        }

        $sectionData['sections'] = $sections;

        $content = $this->view->render('module/layout.html.php', $sectionData);
        return new Response($content);
    }
}
