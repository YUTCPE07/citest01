<?php

// class Myloader extends CI_Loader {

//     public function template($template_name, $vars = array(), $return = FALSE)
//     {
//         if($return):
//         $content = $this->view('template/header', $vars, $return);
//         $content = $this->view($template_name, $vars, $return);
//         $content = $this->view('template/footer', $vars, $return);

//         return $content;
//     else:
//         $this->view('template/header', $vars);
//         $this->view($template_name, $vars);
//         $this->view('template/footer', $vars);
//     endif;
//     }
// }