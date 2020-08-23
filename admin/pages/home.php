<? global $user; ?>
Bem-vindo ao iAdmin, <?= !empty($user->data('name')) ? $user->data('name') : $user->data('username'); ?>!