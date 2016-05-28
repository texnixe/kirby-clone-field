<?php

/**
 * Clone Field for Kirby CMS (v. 2.3.0)
 *
 * @author    Sonja Broda - info@exniq.de
 * @version   0.5
 *
 */

class CloneField extends BaseField {
  static public $fieldname = 'clone';
  static public $assets = array(
    'js' => array(
      'script.js',
    ),
    'css' => array(
      'style.css',
    )
  );

  public function input() {
    $site = kirby()->site();
    if(!$site->multilang() || ($site->multilang() && $site->language() == $site->defaultLanguage())) {

      $html = tpl::load( __DIR__ . DS . 'template.php', $data = array(
        'field' => $this,
        'page' => $this->page(),
      ));

      return $html;

    } else {
      return false;
    }
  }

  public function result() {
    return null;
  }

  public function element() {
    $element = parent::element();
    $element->data('field', self::$fieldname);
    return $element;
  }


  // Routes
  public function routes() {
    return array(
      array(
        'pattern' => 'ajax/(:any)/(:any)/(:any)',
        'method'  => 'get',
        'action' => function($uid, $parent, $newID) {

          $site = kirby()->site();

          if($parent === 'site') {

            $p = page($uid);
            $parent = $site;

          } else {

            $p = page($parent . '/' . $uid);
            $parent = page($parent);

          }
          if($site->multiLanguage()->bool()) {

            $data = $p->content($site->defaultLanguage()->code())->toArray();
            $data['title'] = urldecode($newID);

          } else {

            $data = $p->content()->toArray();
            $data['title'] = urldecode($newID);

          }

          try {

              $newPage = $parent->children()->create(str::slug(urldecode($newID)), $p->intendedTemplate(), $data);

              if($site->multiLanguage()->bool()) {

                foreach($site->languages() as $l) {

                  if($l !== $site->defaultLanguage()) {

                    $data = $p->content($l->code())->toArray();
                    $data['title'] = urldecode($newID);
                    $newPage->update($data, $l->code());

                  }

                }

              }

              return 'The new page has been created' . ' <i class="icon fa fa-close"></i>';

            } catch(Exception $e) {

              return $e->getMessage();

            }
        }
      )
    );
  }

}
