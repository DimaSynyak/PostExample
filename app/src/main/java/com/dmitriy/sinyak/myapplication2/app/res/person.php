<?php
  // Template Name: Person
$currentLang = qtrans_getLanguage();
?>
<?php
if(is_user_logged_in()){
  if ($currentLang == 'ee') {
    wp_redirect(site_url() . '/my-account/');
  }else{
    wp_redirect(site_url() . '/'.$currentLang.'/my-account/');
  }
  exit;
}
?>
<?php
if (count($_POST) > 0) {
  $params = array();
  $meta = array();
  $err = array();

  // Получаем пароль
  if (!empty($_POST['password'])) {
    if (!empty($_POST['confirmation-password'])) {
      if ($_POST['password'] == $_POST['confirmation-password']) {
        $params['user_pass'] = $_POST['password'];
      } else {
        $err['password'] = true;
        $err['confirmation-password'] = true;
      }
    } else {
      $err['confirmation-password'] = true;
    }
  } else {
    $err['password'] = true;
  }

  // Получаем имя
  if (!empty($_POST['user_name'])) {
    $params['user_nicname'] = translit($_POST['user_name']);
    $params['display_name'] = strip_tags(trim($_POST['user_name']));
    $params['nickname'] = strip_tags(trim($_POST['user_name']));
    $meta['billing_first_name'] = strip_tags(trim($_POST['user_name']));
  } else {
    $err['name'] = true;
  }

  // Получаем email
  if (!empty($_POST['email'])) {
    $params['user_email'] = strip_tags(trim($_POST['email']));
    $params['user_login'] = $params['user_email'];
    $meta['billing_email'] = strip_tags(trim($_POST['email']));
  } else {
    $err['email'] = true;
  }

  // Получаем телефон
  if (strlen($_POST['phone']) > 7) {
    $meta['billing_phone'] = strip_tags(trim($_POST['phone']));
  } else {
    $err['phone'] = true;
  }

  // Получаем страну
  if (!empty($_POST['country'])) {
    $meta['billing_country'] = strip_tags(trim($_POST['country']));
  } else {
    $err['country'] = true;
  }

  // Получаем город
  if (!empty($_POST['city'])) {
    $meta['city'] = strip_tags(trim($_POST['city']));
  } else {
    $err['city'] = true;
  }

  // Получаем почтовый индекс
  if (!empty($_POST['billing_postcode'])) {
    $meta['billing_postcode'] = strip_tags(trim($_POST['billing_postcode']));
  } else {
    $err['billing_postcode'] = true;
  }

  // Получаем улицу
  if (!empty($_POST['billing_address_1'])) {
    $meta['billing_address_1'] = strip_tags(trim($_POST['billing_address_1']));
  } else {
    $err['billing_address_1'] = true;
  }

  // Получаем дом
  if (!empty($_POST['billing_address_2'])) {
    $meta['billing_address_2'] = strip_tags(trim($_POST['billing_address_2']));
  } else {
    $err['billing_address_2'] = true;
  }

  // Получаем квартиру/офис
  if (!empty($_POST['billing_address_3'])) {
    $meta['billing_address_3'] = strip_tags(trim($_POST['billing_address_3']));
  } else {
    $err['billing_address_3'] = true;
  }

  $params['role'] = 'subscriber';
  $meta['show_admin_bar_front'] = 'false';

  if (count($err) == 0) {
    // Создаём пользователя
    $user_id = wp_insert_user($params);

    if (!is_wp_error($user_id)) {
      $result = wp_update_user(array('ID' => $user_id, 'user_status' => 1));
      foreach ($meta as $key => $param) {
        update_user_meta($user_id, $key, $param);
      }
      wp_new_user_notification($user_id, $params['user_pass']);
      $err = false;

      $user = wp_signon(array(
        'user_login'    => $params['user_login'],
        'user_password' => $params['user_pass'],
        'remember'      => true,
      ));

      if ( !is_wp_error($user) ){
        if ($currentLang == 'ee') {
          wp_redirect(site_url() . '/restaurant/');
        }else{
          wp_redirect(site_url() . '/'.$currentLang.'/restaurant/');
        }
        exit;
      }

    } else {
      $err['result'] = $user_id->get_error_message();
    }
  }
}
?>
<?php get_header(); ?>
<section class="person">
  <form method="post">
  <div class="breadcrumb">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-12 pr"><a href="<?=__('<!--:ee-->/<!--:--><!--:ru-->/ru/<!--:--><!--:en-->/en/<!--:-->')?>" class="active"><?=__('<!--:ee-->Tagasi kodulehele<!--:--><!--:ru-->Главная<!--:--><!--:en-->Home<!--:-->')?></a> / <span class="current-page"><?=__('<!--:ee-->Isiklik kabinet<!--:--><!--:ru-->Личный кабинет<!--:--><!--:en-->My account<!--:-->')?></span></div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h1><?=__('<!--:ee-->Isiklik kabinet<!--:--><!--:ru-->Личный кабинет<!--:--><!--:en-->My account<!--:-->')?></h1>
        <p><?=__('<!--:ee-->Isiklikke andmeid sisestades kinnitate Teie nende õige olemasolu ning lubate <a href="/">menu24.ee\'l</a> kasutada neid järgmiste tellimuste korral. Veebiressursi lihtsama kasutamiseks palume veenduda selles, et kõik Teie poolt sisestatud andmed on õiged.<!--:--><!--:ru-->Все данные, указанные в Личном кабинете, будут использоваться <a href="/ru/">menu24.ee</a> для автозаполнения формы Ваших будущих Заказов с возможностью их редактирования. Для простоты использования интернет ресурса, рекомендуем вводить верные контактные и другие данные.<!--:--><!--:en-->All the details, provided in My Account, are going to be used by <a href="/en/">menu24.ee</a> to auto fill the forms of your future orders. For your convenience we recommend to use correct contact, address and other details.<!--:-->')?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 login">
        <p>
          <span class="t1"><?=__('<!--:ee-->Nimi:<!--:--><!--:ru-->Имя:<!--:--><!--:en-->Name:<!--:-->')?></span>
          <input type="text" name="user_name" class="i1 <?= $err['name'] ? 'error' : '' ?>" value="<?=$params['display_name']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Email (Login):<!--:--><!--:ru-->Email (Логин):<!--:--><!--:en-->Email (Login):<!--:-->')?></span>
          <input type="email" name="email" class="i2 <?= $err['email'] ? 'error' : '' ?>" value="<?=$_POST['email']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Salasõna:<!--:--><!--:ru-->Пароль:<!--:--><!--:en-->Password:<!--:-->')?></span>
          <input type="password" name="password" class=" i1<?= $err['password'] ? 'error' : '' ?>" value="<?=$_POST['password']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Salasõna kinnitus:<!--:--><!--:ru-->Подтвердить пароль:<!--:--><!--:en-->Confirm Password:<!--:-->')?></span>
          <input type="password" name="confirmation-password" class="i1 <?= $err['confirmation-password'] ? 'error' : '' ?>" value="<?=$_POST['confirmation-password']?>"/>
        <p class="phone"><span class="t1"><?=__('<!--:ee-->Telefon:<!--:--><!--:ru-->Телефон:<!--:--><!--:en-->Phone:<!--:-->')?></span>
          <input type="text" name="phone" class="i3<?= $err['phone'] ? ' error' : '' ?>" value="<?=$_POST['phone']?>">

      </div>
      <div class="col-sm-6 login">
        <p>
        <div class="drop2"><label><?=__('<!--:ee-->Maa:<!--:--><!--:ru-->Страна:<!--:--><!--:en-->Country:<!--:-->')?></label>
          <div id="countries" data-name="country"
               class="bfh-selectbox bfh-countries i2<?= $err['country'] ? ' error' : '' ?>" data-blank="false"
               data-available="" data-flags="true" data-country="<?=$meta['country']?$meta['country']:'EE'?>" style="float: left;
margin-bottom: 15px;"></div>
        </div>
        <p>
          <span class="t1"><?=__('<!--:ee-->Linn:<!--:--><!--:ru-->Город:<!--:--><!--:en-->City:<!--:-->')?></span>
          <input type="text" name="city" class="i2 <?= $err['city'] ? 'error' : '' ?>" value="<?=$meta['city']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Sihtnumber:<!--:--><!--:ru-->Почтовый индекс:<!--:--><!--:en-->Postcode:<!--:-->')?></span>
          <input type="text" name="billing_postcode" class="i4 <?= $err['billing_postcode'] ? 'error' : '' ?>" value="<?=$meta['billing_postcode']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Tänav:<!--:--><!--:ru-->Улица:<!--:--><!--:en-->Street:<!--:-->')?></span>
          <input type="text" name="billing_address_1" class="i2 <?= $err['billing_address_1'] ? 'error' : '' ?>" value="<?=$meta['billing_address_1']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Maja nr.:<!--:--><!--:ru-->Дом:<!--:--><!--:en-->House number:<!--:-->')?></span>
          <input type="text" name="billing_address_2" class="i4 <?= $err['billing_address_2'] ? 'error' : '' ?>" value="<?=$meta['billing_address_2']?>"/>
        <p>
          <span class="t1"><?=__('<!--:ee-->Korteri / kontori:<!--:--><!--:ru-->Квартира / Офис:<!--:--><!--:en-->Flat / office:<!--:-->')?></span>
          <input type="text" name="billing_address_3" class="i4 <?= $err['billing_address_3'] ? 'error' : '' ?>" value="<?=$meta['billing_address_3']?>"/>

      </div>
    </div>
    <div class="row">
      <div class="col-sm-offset-6 col-sm-2"><input id="register_button" type="submit" class="btn" value="<?=__('<!--:ee-->Salvesta<!--:--><!--:en-->Save<!--:--><!--:ru-->СОХРАНИТЬ<!--:-->')?>"/></div>
    </div>
  </div>
  </form>
</section>
<?php get_footer(); ?>
