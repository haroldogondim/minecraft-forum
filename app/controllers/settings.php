<?php
defined('HGTN') or exit;
class Settings 
{
	private static $extensoes = array('.png', '.gif', '.jpg', 'jpeg');
  private static $domain = 'geracaoz.com.br';
  private static $sizes = [1200, 1200];
  // usuário ativado quando cria a conta - 1 para sim, 0 para não. 
  private static $user_activated = 1;
	public static function register()
	{
		if(isset($_REQUEST['submit-register']) && !User::Logado())
		{
			$errors = array();
			$username = post('username');
      $name = post('name');
			$email = trim(post('email'));
			$email_repeat = trim(post('email_repeat'));
			$password = trim(post('password'));
			$password_repeat = trim(post('password_repeat'));
			$password_md5 = password_hash($password, PASSWORD_BCRYPT);
			$codigo_escondido = post('code_hidden');
			$codigo_digitado = post('code_register');
			
			$userExists = (bool) db::NumRows(db::Query(sprintf("SELECT 0 FROM users WHERE username = '%s'", $username)));
			$emailExists = (bool) db::NumRows(db::Query(sprintf("SELECT 0 FROM users WHERE email = '%s'", $email)));
			if(empty($username) || empty($email) || empty($email_repeat) || empty($password) || empty($password_repeat) || empty($codigo_digitado)) {
				$errors[] = 'Nenhum campo pode ou deve ficar em branco.';
			} else {
        if($userExists)
        {
          $errors[] = 'Já existe um usuário cadastrado com esse nome.';
        }
        elseif($emailExists)
        {
          $errors[] = 'Já existe um usuário cadastrado com esse email.';
        }
        else
        {
          if(preg_match('/[^a-zA-Z0-9\_]/', $username)) {
            $errors[] = 'Seu usuário não pode conter espaços, deve conter letras de A-Z, pode conter números de 0-9, e pode conter os caracteres especiais: -_=?!@:,.';
          }
          
          if(strlen($username) < 2 || strlen($username) > 16) {
            $errors[] = 'Seu usuário deve conter entre 3 e 16 caracteres.';
          }
          
          if(!is_numeric($codigo_digitado))
          {
            $errors[] = 'Só são permitidos números no campo do código de verificação.';
          }
          
          if(filter_var($email_repeat, FILTER_VALIDATE_EMAIL) === false)
          {
            $errors[] = 'O email que você digitou é inválido.';
          }
          
          if($password_repeat != $password)
          {
            $errors[] = 'A senha de confirmação está diferente da primeira senha digitada.';
          }
          
          if($email_repeat != $email)
          {
            $errors[] = 'O email de confirmação está diferente do primeiro email digitado.';
          }
          
          if($codigo_digitado != $codigo_escondido)
          {
            $errors[] = 'Os números de confirmação não conferem.';
          }
        }
			}
			
			if(count($errors))
			{
				$retorno = '';
				foreach($errors as $erro)
				{
					$retorno .= '<li>'.$erro.'</li>';
				}
        
        return message('Oops!', $retorno, 'remove', 'warning');
			}
			else
			{
				$generatedKey = md5(generateCode(16));
				$insertUser = db::Query(sprintf("INSERT INTO users(username, password, name, generatedKey, createdAt, lastLoginAt, lastUpdateAtUsername, ip, email, activated) VALUES('%s', '%s', '%s', '%s', '%u', '%u', '%u', '%s', '%s', '%u')", $username, $password_md5, $name, $generatedKey, time(), time(), time(), $_SERVER['REMOTE_ADDR'], $email, self::$user_activated));
				$userId = db::InsertId();
        @mail($email, 'Registro Geração Z', "Olá {$username},\n\nVi que você se registrou no nosso site recentemente. Espero que se divirta! :-)\n\nSegue abaixo os seus dados de registro: \nUsuário: {$username}\nSenha: {$password}\n\n Ahh, queria te lembrar que pra você usar o nosso site corretamente você precisa confirmar sua conta por uma única e simples etapa. É bem fácil. Clique no link abaixo e sua conta será ativada automaticamente. \n\n <a href='http://".self::$domain."/settings/activate/".$generatedKey."'>Clique aqui</a> \n\nAtenciosamente, Equipe Geração Z", 'From: noreply@'.self::$domain.'');
				if($insertUser) {
          User::CreateSession($userId);
					$_SESSION['account_create'] = true;
					header('Location: /index.php');
				} else {
          return message('Oops!', 'Erro ao inserir no banco de dados.', 'remove', 'warning');
				}
			}
		}
	}
  
  public static function register_facebook() {
    if(isset($_REQUEST['register-facebook']) && !User::Logado() && isset($_SESSION['fb_data_retrieve']))
		{
      $errors = array();
      $username = post('username');
      $user_exists = (bool) db::NumRows(db::Query(sprintf("SELECT 0 FROM users WHERE username = '%s'", $username)));
      $name = post('name');
      $email = $_SESSION['fb_data_retrieve']['email'];
      $fb_id = $_SESSION['fb_data_retrieve']['user_id'];
      if(empty($username)) {
        $errors[] = 'O campo de usuário não pode ficar em branco.';
      } else {
          if(strlen($username) < 4 || strlen($username) > 16) {
            $errors[] = 'Seu usuário deve conter entre 3 e 16 caracteres.';
          }
          
          if(preg_match('/[^a-zA-Z0-9\_]/', $username)) {
            $errors[] = 'Seu usuário não pode conter espaços, deve conter letras de A-Z, pode conter números de 0-9, e conter apenas o caractere especial "underline".';
          }
      }
      
      if(!count($errors)) {
        if($user_exists) {
          $errors[] = 'Este nome de usuário já existe. Por favor, tente outro.';
        }
      }
      
      if(count($errors))
			{
				$retorno = '';
				foreach($errors as $erro)
				{
					$retorno .= '<li>'.$erro.'</li>';
				}
        
        return message('Oops!', $retorno, 'remove', 'error');
			}
			else
			{
				$generatedKey = md5(generateCode(16));
        $password = generateCode(16);
        $password_md5 = password_hash($password, PASSWORD_BCRYPT);
				$insertUser = db::Query(sprintf("INSERT INTO users(username, password, name, generatedKey, createdAt, lastLoginAt, lastUpdateAtUsername, ip, email, fb_id, activated) VALUES('%s', '%s', '%s', '%s', '%u', '%u', '%u', '%s', '%s', '%s', '%u')", $username, $password_md5, $name, $generatedKey, time(), time(), time(), $_SERVER['REMOTE_ADDR'], $email, $fb_id, '1'));
				$userId = db::InsertId();
        @mail($email, 'Bem-vindo ao Fórum Geração Z.', "Olá {$username},\n\nVi que você se registrou no nosso site recentemente. Espero que se divirta! :-)\n\nSegue abaixo os seus dados de registro: \nUsuário: {$username}\nSenha: {$password}\n\n Ahh, queria te lembrar que pra você usar o nosso site corretamente você precisa confirmar sua conta por uma única e simples etapa. É bem fácil. Clique no link abaixo e sua conta será ativada automaticamente. \n\n <a href='http://".self::$domain."/settings/activate/".$chave."'>Clique aqui</a> \n\nAtenciosamente, Equipe Geração Z", 'From: noreply@'.self::$domain.'');
				if($insertUser) {
          User::CreateSession($userId);
					$_SESSION['account_create'] = true;
					header('Location: /index.php');
				} else {
          return message('Oops!', 'Erro ao inserir no banco de dados.', 'remove', 'warning');
				}
			}
    }
    
    if(isset($_REQUEST['cancel']) && !User::Logado())
		{
      unset($_SESSION['fb_data_retrieve']);
      header('Location: /login', true);
    }
  }
	
	public static function password() {
		if(isset($_REQUEST['submit-password']) && User::loggedIn()) {
			$errors = array();
			$senha = post('password');
			$nova_senha = post('new_password');
			$nova_senha_repeat = post('repeat_password');
			$senha_encrypt = password_hash($nova_senha, PASSWORD_BCRYPT);
			
			$senha_atual = User::data('password');
			
			if(empty($senha) || empty($nova_senha) || empty($nova_senha_repeat))
			{
				$errors[] = 'Você não digitou algo nos campos.';
			}
			else
			{
				if($nova_senha != $nova_senha_repeat)
				{
					$errors[] = 'O campo de nova senha e repetição de senha não coincidem. Verifique os dados digitados e tente novamente.';
				}
				
				if(!password_verify($senha, $senha_atual))
				{
					$errors[] = 'A senha atual digitada não confere com a sua senha atual.';
				}
			}
			if(count($errors))
			{
				$retornos = '';
				foreach($errors as $erro)
				{
					$retornos .= '<li>'.$erro.'</li>';
				}
        
        return message('Oops!', $retornos, 'remove', 'warning');
			}
			else
			{
				$update = db::Query(sprintf("UPDATE users SET password = '%s' WHERE id = '%s'", $senha_encrypt, User::data('id')));
				if($update) {
					return message('Sucesso!', 'Sua senha foi atualizada com sucesso.', 'checkmark', 'success');
				}
				else
				{
					return message('Oops!', 'Erro ao alterar senha.', 'remove', 'error');
				}
			}
		}
	}
  
  public static function my() {
		if(isset($_REQUEST['submit-my']) && User::loggedIn()) {
			$errors = array();
			$name = post('name');
			$email = post('email');
      
      $data_update = array('name' => $name, 'email' => $email);
			
			if(empty($email))
			{
				$errors[] = 'O campo de e-mail não pode ficar em branco.';
			} else {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
          $errors[] = 'Digite um e-mail válido.';
        } else {
          if(User::data('email') != $email) {
            //$data_update['activated'] = '0';
          }
        }
      }
      
			if(count($errors))
			{
				$retornos = '';
				foreach($errors as $erro)
				{
					$retornos .= '<li>'.$erro.'</li>';
				}
        
        return message('Oops!', $retornos, 'remove', 'warning');
			}
			else
			{
        $update = db::Update("users", $data_update, array("id" => User::data("id")));
				//$update = db::Query(sprintf("UPDATE users SET password = '%s' WHERE id = '%s'", $senha_encrypt, User::data('id')));
				if($update) {
					return message('Sucesso!', 'Dados alterados com sucesso.', 'checkmark', 'success');
				}
				else
				{
					return message('Oops!', 'Erro ao alterar seus dados. Tente novamente.', 'remove', 'error');
				}
			}
		}
	}
	
	public static function signature()
	{
		if(isset($_REQUEST['submit-signature']) && User::Logado())
		{
			$assinatura = post('signature');
			//$stripped = strip_tags($assinatura);
			
			if(strlen($assinatura) > 500) {
				return message('Oops!', 'Sua assinatura não pode conter mais de 500 caracteres.', 'remove', 'warning');
			}
			else
			{
				$update = db::Query(sprintf("UPDATE users SET signature = '%s' WHERE id = '%s'", $assinatura, User::Info('id')));
				if($update) {
					return message('Sucesso!', 'Sua assinatura foi atualizado com sucesso.', 'checkmark', 'success');
				} else {
          
					return message('Oops!', 'Ocorreu um erro ao atualizar sua assinatura.', 'remove', 'error');
				}
			}
		}
	}
	
	public static function changePicture()
	{
		if(isset($_REQUEST['submit-avatar']) && User::Logado())
		{
			// return msg_cor('vermelha', 'Você não fez nada slent buho kkk');
			$errors = array();
			
			$imagem = $_FILES['imagem'];
			$extensao = substr(strtolower($imagem['name']), -4);
			
			if(empty($imagem['name']) || $imagem === false)
			{
				$errors[] = 'Você deve enviar alguma imagem.';
			}
			else
			{
				
				$tamanho = @getimagesize($imagem['tmp_name']);
				
				if(!$tamanho || !is_file($imagem['tmp_name']))
				{
					$errors[] = 'O arquivo que você enviou não é uma imagem, ou encontra-se corrompido.';
				}
				else
				{
					if($tamanho[0] > self::$sizes[0] || $tamanho[1] > self::$sizes[0])
					{
						$errors[] = 'A imagem não pode exceder 1200px de largura ou altura.';
					}
					if ($imagem['size'] > ((1024*1000) * 3))
					{
						$errors[] = 'A sua imagem excedeu o tamanho máximo de 3mb.';
					}

					if (!in_array($extensao, self::$extensoes))
					{
						$errors[] = 'A extensão que você utilizou não é permitida. As únicas permitidas são: ' . str_replace('.', '', strtoupper(implode(', ', self::$extensoes))) . '.';
					}
				}
			}
			
			$imagem_modify = 'avatar-'. md5(uniqid(rand(), true)) .$extensao;
			$local_imagem = 'assets/uploads/avatar/'.$imagem_modify;
			
			$parte_retorno = '';
			if(count($errors))
			{
				foreach($errors as $erro)
				{
					$parte_retorno .= '<li>'.$erro.'</li>';
				}
				
				return message('Oops', $parte_retorno, 'remove', 'error');
			}
			else
			{
				if(move_uploaded_file($imagem['tmp_name'], $local_imagem))
				{
					if(is_file(sprintf('assets/uploads/avatar/%s', User::data('avatar'))) && User::data('avatar') != 'default.png') { 
						unlink(sprintf('assets/uploads/avatar/%s', User::data('avatar'))); 
					}
					$update_avatar = db::Query(sprintf("UPDATE users SET avatar = '%s' WHERE id = '%u'", $imagem_modify, User::data('id')));
					if($update_avatar)
					{
            Cache::exclui('topic-*');
						return message('Sucesso!', 'Seu avatar foi atualizado com sucesso.', 'checkmark', 'success');
					}
					else
					{
						return message('Oops!', 'Ocorreu um erro ao atualizar seu avatar.', 'remove', 'error');
					}
				}
				else
				{
					return message('Oops!', 'Não conseguimos fazer o upload da sua imagem. Verifique a integridade do arquivo e tente novamente.', 'remove', 'error');
				}
			}
		}
	}
  
  private static function sendMail() {
    if(!User::loggedIn()) {
      exit;
    }
    
    $userKey = User::data('generatedKey');
    @mail(User::data('email'), "Confirme sua conta no Fórum GeraçãoZ", "Olá,\n Vimos que você solicitou um novo email para solicitar a confirmação da sua conta.\n\n <a href='http://".self::$domain."/settings/activate/".$userKey."' target='_blank'>Clique aqui</a> para confirmar sua conta. \n\n Atenciosamente,  Equipe Geração Z", "From: Equipe GeraçãoZ <noreply@geracaoz.com.br>" . "\r\n");
  }
  
  public static function sendMailActivation() {
    if(!User::loggedIn()) {
      exit;
    }
    
    if(isset($_REQUEST['send-mail'])) {
      if(!isset($_SESSION['attemps_send_mail']) || empty($_SESSION['attemps_send_mail']))
      {
        $_SESSION['attemps_send_mail'] = time() - 301;
      }
      
      if($_SESSION['attemps_send_mail'] > time() - 300)
      {
        return message("Oops!", "Você solicitou um envio um e-mail recentemente. Tente novamente em 5 minutos.", 'remove', 'warning', true);
      }
      
      $_SESSION['attemps_send_mail'] = time();
      
      self::sendMail();
      
      return message('Sucesso!', 'Enviamos um novo e-mail para você. Dê uma olhada também na sua caixa de spam, pois o email pode ter ido para lá.', 'checkmark', 'success', true);
    }
  }
  
  public static function checkCode() {
    if(!User::loggedIn()) {
      exit;
    }
    if(get('code') == User::data('generatedKey')) {
      db::Update("users", array("activated" => "1"), array("id" => User::data('id')));
      return message('Sucesso!', 'Parabéns! Sua conta foi ativada com sucesso.', 'checkmark', 'success', true);
    } else {
      return message("Oops!", "O código de ativação de conta é inválido.", 'remove', 'warning',true);
    }
  }
}