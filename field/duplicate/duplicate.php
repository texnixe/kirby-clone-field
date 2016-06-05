<?php

/**
* Clone Field for Kirby CMS (v. 2.3.0)
*
* @author    Sonja Broda - info@exniq.de
* @version   1.1
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

          // fetch all files
          $files = $this->page()->files();

          // get page data depending on site type (single or multi-lingual)
          if($site->multilang()) {
            $lDefaultCode = $site->defaultLanguage()->code();
            $data = $page->content($lDefaultCode)->toArray();
            $data['title'] = urldecode($newID);

          } else {

            $data = $page->content()->toArray();
            $data['title'] = urldecode($newID);

          }
          // try to create the new page
          try {

            $newPage = $page->siblings()->create(str::slug(urldecode($newID)), $page->intendedTemplate(), $data);

            if($site->multilang()) {

              foreach($site->languages() as $l) {
                if($l !== $site->defaultLanguage()) {
                  $data = $page->content($l->code())->toArray();
                  $data['title'] = urldecode($newID);
                  $newPage->update($data, $l->code());
                }
              }
            }

            // copy all page files to the new location
            foreach($files as $file) {
              $file->copy(kirby()->roots()->content() . '/' . $newPage->diruri() . "/" . $file->filename());
            }

            // copy meta files to new location
            $metaFiles = $page->inventory()['meta'];
            $source = kirby()->roots()->content() . DS . $page->diruri();
            $target = kirby()->roots()->content() . DS . $newPage->diruri();

            // different ways to get metafiles in single and multi-lingual environments
            foreach($metaFiles as $file) {
              if(is_array($file)) {
                foreach($file as $key => $filename) {
                  f::copy($source . "/" . $filename, $target . "/" . $filename);
                }

              } else {
                f::copy($source . "/" . $file, $target . "/" . $file);
              }
            }

          // trigger panel.page.create event
          kirby()->trigger('panel.page.create', $newPage);

          $response = array(
            'message' => 'The page was successfully created. ',
            'class' => 'success',
            'uri' => $newPage->uri()
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
