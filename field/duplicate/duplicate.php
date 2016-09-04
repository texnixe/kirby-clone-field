<?php

/**
* Clone Field for Kirby CMS (v. 2.3.0)
*
* @author    Sonja Broda - info@exniq.de
* @version   1.3
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

  public $copytree = false;
  public $copyfile = true;

  public function input() {
    // Load template with arguments
    $site = kirby()->site();
    // in a multilang installation, only show field in default language
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

  public function getFiles() {
    return $this->page()->files();
  }

  public function getData($lang = null) {
    $site = kirby()->site();
    if($site->multilang()) {
      $lDefaultCode = $site->defaultLanguage()->code();
      return $this->page()->content($lDefaultCode)->toArray();
    } if(is_string($lang)) {
      return $this->page()->content($lang)->toArray();
    } else {
      return $this->page()->content()->toArray();
    }

  }

  public function updatePage($newPage, $newID) {
    $site = kirby()->site();
    foreach($site->languages() as $l) {
      if($l !== $site->defaultLanguage()) {
        $data = $this->getData($l->code());
        $data['title'] = urldecode($newID);
        try {
          $newPage->update($data, $l->code());
          return true;
        } catch(Exception $e) {
          return false;
        }
      }
    }
  }

  public function copyFiles($files, $newPage) {
    foreach($files as $file) {
      try {
        $file->copy(kirby()->roots()->content() . '/' . $newPage->diruri() . "/" . $file->filename());
      } catch (Exception $e) {
        return false;
      }
    }
    return true;
  }

  public function copyMetaFiles($newPage) {
    $metaFiles = $this->page()->inventory()['meta'];
    $source = kirby()->roots()->content() . DS . $this->page()->diruri();
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
  }

  public function copyPage($newID) {

    $site = kirby()->site();
    $page = $this->page();

    // fetch all files
    $files = $this->getfiles();

    // get page data
    $data = $this->getData();
    $data['title'] = urldecode($newID);


    try {

      $newPage = $page->siblings()->create(str::slug(urldecode($newID)), $page->intendedTemplate(), $data);

      if($site->multilang()) {
        $this->updatePage($newPage, $newID);
      }

      // copy all page files to the new location
      if($this->copyfile()) {
        $this->copyFiles($files, $newPage);
        $this->copyMetaFiles($newPage);
      }

      // trigger panel.page.create event
      kirby()->trigger('panel.page.create', $newPage);


      $response = array(
        'message' => 'The page was successfully created. ',
        'class' => 'success',
        'uri' => $newPage->uri()
      );

    } catch(Exception $e) {

      $response = array(
        'message' => $e->getMessage(),
        'class' => 'error'
      );

    }


    return $response;
  }

  public function copyTree($newID) {

    $slug = str::slug(urldecode($newID));
    $sourcePath = kirby()->roots->content() . DS . $this->page()->diruri();
    $pathWithoutNumber = kirby()->roots->content() . DS . $this->page()->uri();
    $destPath = kirby()->roots->content() . DS . $this->page()->parent()->diruri() . $slug;

    if($pathWithoutNumber === $destPath) {

      $response = array(
        'message' => 'The page already exists',
        'class' => 'error'
      );

    } else {

      if(!dir::copy($sourcePath, $destPath)) {
        $response = array(
          'message' => $e->getMessage(),
          'class' => 'error'
        );
      } else {
        $newPage = new Page($this->page()->parent(), $slug);



        try {
          $newPage->update(array(
            'title' => urldecode($newID),
          ));
          // trigger hook for each page in the tree
          foreach($newPage->index() as $p) {
            kirby()->trigger('panel.page.create', $p);
          }
          $response = array(
            'message' => 'The folder was successfully created.',
            'class' => 'success',
            'uri' => $newPage->uri(),
          );
        } catch(Exception $e) {

          $response = array(
            'message' => $e->getMessage(),
            'class' => 'error'
          );
        }
    }

  }

  return $response;
}

  // Routes
  public function routes() {
    return array(
      array(
        'pattern' => 'api/duplicate/(:any)',
        'method'  => 'GET',
        'action' => function($newID) {

          if($this->copytree) {
            $response = $this->copyTree($newID);
          } else {
            $response = $this->copyPage($newID);
          }


          return json_encode($response);

        }
        )
      );

    }
  }
