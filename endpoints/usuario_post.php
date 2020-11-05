<?php

function api_usuario_post($request) {

  $nome = $request['nome'];
  $email = sanitize_email($request['email']);
  $senha = $request['senha'];
  $rua = sanitize_text_field($request['rua']);
  $cep = sanitize_text_field($request['cep']);
  $cidade = sanitize_text_field($request['cidade']);
  $numero = sanitize_text_field($request['numero']);
  $bairro = sanitize_text_field($request['bairro']);
  $estado = sanitize_text_field($request['estado']);

  $user_exists = username_exists($email);
  $email_exists = email_exists($email);

  if(!$user_exists && !$email_exists && $email && $senha){
    $id = wp_create_user($email, $senha, $email);

    $response = array(
      'ID' => $id,
      'display_name' => $nome,
      'first_name' => $nome,
      'role' => 'subscriber',
    );
    wp_update_user($response);

    update_user_meta($id, 'cep', $cep);
    update_user_meta($id, 'rua', $rua);
    update_user_meta($id, 'numero', $numero);
    update_user_meta($id, 'bairro', $bairro);
    update_user_meta($id, 'cidade', $cidade);
    update_user_meta($id, 'estado', $estado);
  }else{
    $response = new WP_Error('email', 'Email já cadastrado.', array('status' => 403));
  }
  return rest_ensure_response($response);
}

function registrar_api_usuario_post() {
  register_rest_route('api', '/usuario', array(
    array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_usuario_post',
    ),
  ));
}

add_action('rest_api_init', 'registrar_api_usuario_post');


?>