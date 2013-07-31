<?php
class PostController extends FrontendBaseController {
    public function executeDefault() {
        return $this->executeDetail();
    }

    public function executeDetail() {
        if(!($post = Posts::retrieveById($this->request()->get('id')))) {
            $this->raise404();
        }

        $post->setHits($post->getHits() + 1);
        $post->save(false);

        $term = Terms::retrieveById($post->getTermId());
        if (($viewProp = $term->getProperty('post_view'))
            && $this->view()->checkViewFileExist($this->getTemplatePath() .'/controllers/'.$this->_path .$viewProp->getValue())) {
            $this->setView($viewProp->getValue());
        }

        $this->view()->assign(array(
            'post' => $post,
            'term' => $term
        ));

        return $this->renderComponent();
    }

    public function executeDownload() {
        if (!($attach = PostAttachments::retrieveById($this->request()->get('id')))) {
            $this->raise404(t('File not found'));
        }

        $path = PUBLIC_DIR .DIRECTORY_SEPARATOR .rtrim($attach->getFile(), '/');
        if (!file_exists($path)) {
            $this->raise404(t('File not found'));
        }

        $attach->setHits($attach->getHits() + 1);
        $attach->save(false);

        $len = filesize($path);
        $filename = basename($path);
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $path) ;
        finfo_close($finfo);

        ob_end_clean();
        $response = \Flywheel\Factory::getResponse();
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Expires', '0', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Type', $mime, true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$filename.';', true);
        $response->setHeader('Content-Transfer-Encoding', 'binary', true);
        $response->setHeader('Content-Length', $len, true);
        $response->sendContent(readfile($path));
        $response->send();

        \Flywheel\Base::end();
    }
}