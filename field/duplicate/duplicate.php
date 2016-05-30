<?php

/**
 * Clone Field for Kirby CMS (v. 2.3.0)
 *
 * @author    Sonja Broda - info@exniq.de
 * @version   1.0
 *
 */

class DuplicateField extends BaseField {
  static public $fieldname = 'duplicate';
  static public $assets = array(
   'js' => array(
      'script.js',
    ),
    'css' => array(
      'style.css',
    )
  );

  public function input() {
    // Load template with arguments
    $site = kirby()->site();
    if(!$site->multilang() || ($site->multilang() && $site->language() == $site->defaultLanguage())) {
      $html = tpl::load( __DIR__ . DS . 'template.php', $data = array(
        'field' => $this,
        'page' => $this->page(),
        'placeholder'  => $this->i18n($this->placeholder()),
        'buttontext' => $this->i18n($this->buttontext()),
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

  public function getFiles($page) {
    $files = $page->files();
  }


  // Routes
  public function routes() {
    return array(
      array(
        'pattern' => 'ajax/(:any)',
        'method'  => 'GET',
        'action' => function($newID) {

          $site = kirby()->site();
          $page = $this->page();
          $files = $this->page()->files();

          if($site->multiLanguage()->bool()) {

            $data = $page->content($site->defaultLanguage()->code())->toArray();
            $data['title'] = urldecode($newID);

          } else {

            $data = $page->content()->toArray();
            $data['title'] = urldecode($newID);

          }

          try {

              $newPage = $page->parent()->children()->create(str::slug(urldecode($newID)), $page->intendedTemplate(), $data);

              if($site->multiLanguage()->bool()) {

                foreach($site->languages() as $l) {

                  if($l !== $site->defaultLanguage()) {

                    $data = $page->content($l->code())->toArray();
                    $data['title'] = urldecode($newID);
                    $newPage->update($data, $l->code());

                  }

                }

              }
              foreach($files as $file) {

                $file->copy(kirby()->roots()->content() . '/' . $newPage->diruri() . "/" . $file->filename());
              }

              kirby()->trigger('panel.page.create', $newPage);

              $response = array(
                'message' => 'The page was successfully created',
                'class' => 'success'
              );

              return json_encode($response);


            } catch(Exception $e) {

              $response = array(
                'message' => $e->getMessage(),
                'class' => 'error'
              );

              return json_encode($response);

            }

        }
      )
    );
  }

}
