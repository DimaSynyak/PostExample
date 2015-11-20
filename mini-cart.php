<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>
<div id="cart-1" class="cart-1" data-lang="<?=qtrans_getLanguage()?>">
  <span class="back"></span>
  <h4><?=__('<!--:ee-->Teie tellimus<!--:--><!--:ru-->Ваш Заказ<!--:--><!--:en-->Your order<!--:-->')?></h4>
  <span class="clear"><?=__('<!--:ee-->tühista ostukorvi<!--:--><!--:ru-->очистить корзину<!--:--><!--:en-->delete all items<!--:-->')?></span>
  <div class="zakazu">
    <table>
    <?php if ( ! WC()->cart->is_empty() ) : ?>

      <?php
        $restaurants = array();
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
          $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
          $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

          if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

            $product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
            $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

            $product_restaurants = wp_get_post_terms( $_product->id, 'pa_restaurant' );
            if(!isset($restaurants[$product_restaurants[0]->slug])){
              $restaurant = new WP_Query(array(
                'post_type' => 'restaurant',
                'posts_per_page' => 1,
                'meta_query' => array(
                  array(
                    'key' => 'restaurant_term',
                    'value' => $product_restaurants[0]->term_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                  )
                )
              ));
              $restaurants[$product_restaurants[0]->slug] = array(
                'delivery_cost' => get_post_meta($restaurant->posts[0]->ID, 'restaurant_delivery_cost', true),
                'min_order' => get_post_meta($restaurant->posts[0]->ID, 'restaurant_min_order', true) - $_product->price * $cart_item['quantity']
              );
            }else{
              $restaurants[$product_restaurants[0]->slug]['min_order'] -= $_product->price * $cart_item['quantity'];
            }

            ?>

                <tr class="cart_item">
                  <td>
                    <?php
                      echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                        '<a href="%s" class="remove del" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                        esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
                        __( 'Remove this item', 'woocommerce' ),
                        esc_attr( $product_id ),
                        esc_attr( $_product->get_sku() )
                      ), $cart_item_key );
                    ?>
                  </td>
                  <td><span class="plus"></span></td>
                  <td>
                    <span class="kolzak"><?=$cart_item['quantity']?></span>
                  </td>
                  <td><span class="minus"></span></td>
                  <td>
                    <?=__($product_name)?>
                  </td>
                  <td>
                    <span class="summa"><?=$product_price?></span>
                  </td>
                </tr>

            <?php
          }
        }
      ?>

    <?php else : ?>

      <tr class="empty"><td><?=__('<!--:ee-->Ostukorv on tühi<!--:--><!--:ru-->Корзина пуста<!--:--><!--:en-->Cart is empty<!--:-->')?></td></tr>

    <?php endif; ?>

    </table>
  </div><!-- end product list -->

  <?php if ( ! WC()->cart->is_empty() ) : ?>
    <?php
      $delivery_cost = 0;
      $min_order = 0;
      foreach($restaurants as $restaurant){
        $delivery_cost += $restaurant['delivery_cost'];
        if($restaurant['min_order'] > 0)
          $min_order += $restaurant['min_order'];
      }
      WC()->cart->shipping_total = $delivery_cost;
    ?>
    <?php if($min_order > 0){ ?>
      <p><?=__('<!--:ee-->minimaal tellimuse summani on puudu<!--:--><!--:ru-->до мин. суммы заказа недостаточно<!--:--><!--:en-->to the min. amount of the order is not enough<!--:-->')?><span><?=$min_order?> €</span></p>
    <?php }else{ ?>
      <p>&nbsp;</p>
    <?php } ?>
    <p class="dos"><?=__('<!--:ee-->Kohaletoimetamine<!--:--><!--:ru-->Доставка<!--:--><!--:en-->Delivery<!--:-->')?><span><?=$delivery_cost?> €</span></p>
    <p class="opl"><?=__('<!--:ee-->Kokku<!--:--><!--:ru-->Итого<!--:--><!--:en-->Total<!--:-->')?><?=WC()->cart->get_cart_subtotal(true)?></p>
  <?php if($min_order <= 0){ ?>
    <button class="btn"><?=__('<!--:ee-->Edasi<!--:--><!--:ru-->Далее<!--:--><!--:en-->Next<!--:-->')?></button>
  <?php } ?>
    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

    <p class="buttons">
      <a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button wc-forward"><?php _e( 'View Cart', 'woocommerce' ); ?></a>
      <a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button checkout wc-forward"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
    </p>

  <?php endif; ?>

</div>

<div id="cart-2" class="cart-2">
  <span class="back"></span>
  <h4><?=__('<!--:ee-->Teie tellimus<!--:--><!--:ru-->Ваш Заказ<!--:--><!--:en-->Your order<!--:-->')?></h4>
  <span class="clear"><?=__('<!--:ee-->tühista ostukorvi<!--:--><!--:ru-->очистить корзину<!--:--><!--:en-->delete all items<!--:-->')?></span>
  <?php
    $checkout = WC()->checkout();
  ?>
  <?php $get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>
  <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">
  <div class="zakazu">
    <div class="sposdos">
      <button class="btn active" type="button"><?=__('<!--:ee-->kohaletoimetamisega<!--:--><!--:ru-->с доставкой<!--:--><!--:en-->delivery<!--:-->')?></button>
      <button class="btn" type="button"><?=__('<!--:ee-->tulen ise järgi <!--:--><!--:ru-->самовывоз<!--:--><!--:en-->pickup<!--:-->')?></button>
      <input type="hidden" name="billing_delivery" id="billing_delivery" value="<?=__('<!--:ee-->kohaletoimetamisega<!--:--><!--:ru-->с доставкой<!--:--><!--:en-->delivery<!--:-->')?>"/>
    </div>
    <div class="dannue">

      <?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

        <?php //do_action( 'woocommerce_checkout_billing' ); ?>

      <?php endif; ?>

      <?php
        if(is_user_logged_in()){
          $user = wp_get_current_user();
          $meta = get_user_meta($user->ID);
        }else{
          $meta = array();
        }
      ?>

      <p class="form-row form-row  validate-required" id="billing_first_name_field">
        <label for="billing_first_name" class=""><?=__('<!--:ee-->Sisestage<br class="visible-xs"> oma nimi:<!--:--><!--:ru-->Введите<br class="visible-xs"> Имя:<!--:--><!--:en-->Name:<!--:-->')?></label>
        <input class="input-text " name="billing_first_name" id="billing_first_name" placeholder="" value="<?=$meta['billing_first_name'][0]?$meta['billing_first_name'][0]:''?>" type="text">
      </p>

      <div>
        <input name="billing_country" id="billing_country" type="hidden" value="EE">
        <label><?=__('<!--:ee-->Sisestage<br class="visible-xs"> linn:<!--:--><!--:ru-->Выберите<br class="visible-xs"> город:<!--:--><!--:en-->City:<!--:-->')?></label>
        <div class="dropdown">
          <input name="billing_city" id="billing_city" type="hidden" value="<?=__('<!--:ee-->Tallinn<!--:--><!--:ru-->Таллин<!--:--><!--:en-->Tallinn<!--:-->')?><?//=$meta?$meta['billing_city'][0]:''?>">
          <a aria-expanded="false" id="dLabel1" data-toggle="dropdown" role="button" href="#"><?=__('<!--:ee-->Tallinn<!--:--><!--:ru-->Таллин<!--:--><!--:en-->Tallinn<!--:-->')?><?//=$meta['billing_city'][0]?$meta['billing_city'][0]:'выбор'?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel1">
            <li><a data-target="Таллин"><?=__('<!--:ee-->Tallinn<!--:--><!--:ru-->Таллин<!--:--><!--:en-->Tallinn<!--:-->')?></a></li>
          </ul>
        </div>
      </div>

      <p class="form-row form-row " id="billing_address_1_field">
        <label for="billing_address_1" class=""><?=__('<!--:ee-->Tänav:<!--:--><!--:ru-->Улица:<!--:--><!--:en-->Street:<!--:-->')?></label>
        <input class="input-text " name="billing_address_1" id="billing_address_1" placeholder="" value="<?=$meta?$meta['billing_address_1'][0]:''?>" type="text">
      </p>

      <div class="hom">
        <label class="l3"><?=__('<!--:ee-->Maja<br>number<!--:--><!--:ru-->Дом<!--:--><!--:en-->House<br>number<!--:-->')?></label>
        <input class="input-text" name="billing_address_2" id="billing_address_2" placeholder="" value="<?=$meta?$meta['billing_address_2'][0]:''?>" type="text">
        <label class="l3"><?=__('<!--:ee-->Korter<br> Kontor<!--:--><!--:ru-->Квартира<br> Офис<!--:--><!--:en-->Flat<br> Office<!--:-->')?></label>
        <input class="input-text " name="billing_address_3" id="billing_address_3" placeholder="" value="<?=$meta?$meta['billing_address_3'][0]:''?>" type="text">
      </div>

      <div>
        <label class="pickup"><?=__('<!--:ee-->Tulen järgi:<!--:--><!--:ru-->Заберу в:<!--:--><!--:en-->Pickup:<!--:-->')?></label>
        <label class="delivery"><?=__('<!--:ee-->Kohaletoimetamine:<!--:--><!--:ru-->Доставка на:<!--:--><!--:en-->Delivery:<!--:-->')?></label>
        <div class="form-group">
          <div class='input-group date' id='datetimepicker' data-locale="<?=__('<!--:ee-->et<!--:--><!--:ru-->ru<!--:--><!--:en-->en<!--:-->')?>">
            <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
            </span>
            <input type='text' class="form-control" name="billing_time"/>
          </div>
        </div>
      </div>

      <p class="form-row form-row  validate-required" id="billing_email_field">
        <label for="billing_email" class="">Email:</label>
        <input class="input-text " name="billing_email" id="billing_email" placeholder="" value="<?=$meta?$meta['billing_email'][0]:''?>" type="text">
      </p>

      <p class="form-row form-row  validate-required" id="billing_phone_field">
        <label class="billing_phone 15"><?=__('<!--:ee-->Telefon:<!--:--><!--:ru-->Телефон:<!--:--><!--:en-->Phone:<!--:-->')?></label>
        <input class="input-text " name="billing_phone" id="billing_phone" placeholder="" value="<?=$meta?$meta['billing_phone'][0]:''?>" type="text" placeholder="(   )">
      </p>

    </div>
  </div>

  <?php if($min_order > 0){ ?>
    <p><?=__('<!--:ee-->minimaal tellimuse summani on puudu<!--:--><!--:ru-->до мин. суммы заказа недостаточно<!--:--><!--:en-->to the min. amount of the order is not enough<!--:-->')?><span><?=$min_order?> €</span></p>
  <?php }else{ ?>
    <p>&nbsp;</p>
  <?php } ?>
  <p class="dos"><?=__('<!--:ee-->Kasutustingimused<!--:--><!--:ru-->Доставка<!--:--><!--:en-->Delivery<!--:-->')?><span><?=$delivery_cost?> €</span></p>

  <p class="opl"><?=__('<!--:ee-->Kokku<!--:--><!--:ru-->Итого<!--:--><!--:en-->Total<!--:-->')?><?=WC()->cart->get_cart_subtotal(true)?></p>

    <p><input type="checkbox" id="terms_of_use"/><label for="terms_of_use"><?=__('<!--:ee-->Kasutustingimustega olen <a href="/terms_of_use/">tutvunud ja nõus</a>.<!--:--><!--:ru-->Я ознакомплен и согласен с <a href="/terms_of_use/">правилами пользования сайтом</a><!--:--><!--:en-->I have read and agree to the <a href="/terms_of_use/">Terms of Use</a><!--:-->')?></label></p>
  <div id="order_review">
    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
  </div>

  </form>
  <?php global $woocommerce; ?>
  <input type="hidden" value="<?=$woocommerce->cart->get_cart_contents_count()?>" id="cart_count"/>
</div>
<?php do_action( 'woocommerce_after_mini_cart' ); ?>

