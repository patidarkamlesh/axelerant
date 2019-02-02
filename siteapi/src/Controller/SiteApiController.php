<?php

/**
 * @file
 * SiteApiController file to create JSON Page
 */

namespace Drupal\siteapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

Class SiteApiController extends ControllerBase {
  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

    /**
     * Class Constructor
     */
    public function __construct(ConfigFactory $configFactory) {
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        // Instantiates this form class.
        return new static(
                // Load the service required to construct this class.
                $container->get('config.factory')
        );
    }

    /**
     * To Create JSON Page
     * @params
     * $siteapi: Site API which is Available on Site information page
     * $id: Node id
     */
    public function pageJson($siteapi, $id) {
        $node = Node::load($id);
        $siteInfoApi = $this->configFactory->get('system.site')->get('siteapikey');
        if (!empty($node) && ($siteInfoApi == $siteapi)) {
            if ($node->get('type')->target_id == 'page') {
                $json_data = [];
                $json_data['data'][] = [
                    'type' => $node->get('type')->target_id,
                    'id' => $node->get('nid')->value,
                    'attributes' => [
                        'title' => $node->get('title')->value,
                        'content' => $node->get('body')->value,
                    ],
                ];
                return new JsonResponse($json_data);
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
            }
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }

}
