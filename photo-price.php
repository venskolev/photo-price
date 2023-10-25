<?php
/*
Plugin Name: Photo Price
Description: Photo Price is a versatile WooCommerce extension that allows you to add custom pricing to your products, giving you the flexibility to set prices as you wish. It's an ideal solution for photographers, as well as creators of intellectual products, allowing them to tailor pricing to their specific needs.
Version: 1.0
Author: AlfaTrex - Vens Kolev
*/

// Функция за добавяне на административен раздел в менюто
function photo_price_admin_menu() {
    add_menu_page(
        'Photo Price Settings',
        'Photo Price',
        'manage_options',
        'photo-price-settings',
        'photo_price_settings_page',
        'dashicons-admin-generic'    );
}
add_action('admin_menu', 'photo_price_admin_menu');

// Функция за създаване на административна страница за настройки
function photo_price_settings_page() {
  if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  // Запазете настройките, когато формата е изпратена
  if (isset($_POST['photo_price_submit'])) {
      update_option('photo_price', $_POST['photo_price']);
      echo '<div class="updated"><p>Settings saved</p></div>';
  }
  // Премахване на плъгина
  if (isset($_POST['photo_price_delete_data'])) {
    delete_option('photo_price');
    echo '<div class="updated"><p>Data deleted</p></div>';
}
  
  // Извлечете текущите настройки
  $photo_price_settings = get_option('photo_price');
  ?>
  <div class="wrap">
      
<style>
        .text-block {
            width: 80%;
            margin: 0 auto;
            text-align: center;
            background-color: #f2f2f2;
            padding: 20px;
            box-shadow: 5px 5px 15px 0 rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 24px;
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
        }
    </style>
<div class="text-block">
        <h1>AlfaTrex - Your Creative Powerhouse in Germany</h1>
        <p>Unleash limitless creativity and digital excellence with AlfaTrex. We're your one-stop shop for web design, photography, graphic design, marketing, and more. <br> From creating stunning websites that engage your audience to capturing timeless moments through photography, we're dedicated to turning your ideas into compelling visuals.<br> Our expert graphic designers bring your brand to life, while our marketing wizards make your online presence shine. At AlfaTrex, we're more than a studio; we're your creative partner.<br> Experience the AlfaTrex difference today.</p>
<code>Description: Photo Price is a versatile WooCommerce extension that allows you to add custom pricing to your products, giving you the flexibility to set prices as you wish. It's an ideal solution for photographers, as well as creators of intellectual products, allowing them to tailor pricing to their specific needs.</code>
<h4><a href="https://alfatrex.com" target="_blank">AlfaTrex</a> - Vens Kolev</h4>
    </div>
<h2>Photo Price Settings</h2>
      <form method="post" action="">
          <label>Where to display:</label><br>
          <input type="radio" name="photo_price[display_location]" value="title" <?php checked('title', $photo_price_settings['display_location']); ?>> Display after title<br>
          <input type="radio" name="photo_price[display_location]" value="description" <?php checked('description', $photo_price_settings['display_location']); ?>> Display in description<br>
          
          <h3>Currency Settings</h3>
          <label for="photo_price[currency]">Currency:</label>
          <input type="text" name="photo_price[currency]" value="<?php echo esc_attr($photo_price_settings['currency']); ?>" class="regular-text" />
          <br><br>
          <label>Currency Symbol Placement:</label><br>
          <input type="radio" name="photo_price[symbol_placement]" value="before" <?php checked('before', $photo_price_settings['symbol_placement']); ?>> Before the price<br>
          <input type="radio" name="photo_price[symbol_placement]" value="after" <?php checked('after', $photo_price_settings['symbol_placement']); ?>> After the price<br>
          <h3>Style Settings</h3>
          <label for="photo_price[color]">Text Color:</label>
          <input type="text" name="photo_price[color]" value="<?php echo esc_attr($photo_price_settings['color']); ?>" class="regular-text" />
          <br><br>
          <h3>Uninstall</h3>
            <p>If you want to delete all data associated with this plugin, click the button below.</p>
            <input type="submit" name="photo_price_delete_data" class="button" value="Delete Data">
          <br><br>
          <input type="submit" name="photo_price_submit" class="button-primary" value="Save Settings">
      </form>
      
  </div>
  <?php
}

// Функция за интеграция с WooCommerce
function integrate_with_woocommerce() {
  // Вашият код за интеграция с WooCommerce тук
}
add_action('woocommerce_init', 'integrate_with_woocommerce');

function display_custom_price_after_product_title() {
  global $product;

  // Извлечете настройките на цената от настройките на вашия плъгин
  $photo_price_settings = get_option('photo_price');
  $currency = $photo_price_settings['currency'];
  $symbol_placement = $photo_price_settings['symbol_placement'];
  $text_color = $photo_price_settings['color'];

  if ($product) {
      if ($photo_price_settings['display_location'] === 'title') {
          // Показване на цената след съществуващия заглавен елемент
          $style = 'style="color: ' . $text_color . ';"'; // Генерирайте стилове за текстовия цвят
          $product_price = get_post_meta($product->get_id(), '_custom_product_price', true);
          echo '<script>
  jQuery(document).ready(function($) {
    $(".product_title.entry-title").after(`<h3 ' . $style . '>' . format_price($product_price, $currency, $symbol_placement) . '</h3>`);
  });
</script>';
      } elseif ($photo_price_settings['display_location'] === 'description') {
          // Показване на цената в описанието
          $product_price = get_post_meta($product->get_id(), '_custom_product_price', true);
          echo '<script>
              jQuery(document).ready(function($) {
                  $(".product_meta").after("<h3>' . format_price($product_price, $currency, $symbol_placement) . '</h3>");
              });
          </script>';
      }
  }
}
add_action('woocommerce_single_product_summary', 'display_custom_price_after_product_title', 5);

// Функция за съхранение на цената в мета данни на продукта
function save_product_custom_price($post_id) {
  if (isset($_POST['custom_product_price'])) {
      $custom_price = sanitize_text_field($_POST['custom_product_price']);
      update_post_meta($post_id, '_custom_product_price', $custom_price);
  }
}

add_action('save_post', 'save_product_custom_price');

// Функция за форматиране на цената с валутата и символа на валута
function format_price($price, $currency, $symbol_placement) {
  if ($symbol_placement === 'before') {
      return $currency . $price;
  } else {
      return $price . $currency;
  }
}

// Функция за добавяне на текстово поле за цената
function add_product_custom_price_field() {
    woocommerce_wp_text_input(
        array(
            'id' => 'custom_product_price',
            'label' => 'Fotoshooting Price (€)',
            'desc_tip' => 'true',
            'description' => 'Enter a price for the photo shoot, divide the amount with a decimal point.',
            'type' => 'number',
            'custom_attributes' => array('step' => 'any')
        )
    );
    if (isset($_POST['custom_product_price'])) {
        $custom_price = sanitize_text_field($_POST['custom_product_price']);
        update_post_meta($product_id, '_custom_product_price', $custom_price);
    }
}

add_action('woocommerce_product_options_general_product_data', 'add_product_custom_price_field');

// Зареждане на цената при зареждане на страницата на продукта
function load_custom_price_on_product_edit() {
    if (isset($_GET['post'])) {
        $product_id = $_GET['post'];
        $custom_price = get_post_meta($product_id, '_custom_product_price', true);
        if ($custom_price !== '') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#custom_product_price').val('<?php echo esc_attr($custom_price); ?>');
                });
            </script>
            <?php
        }
    }
}

add_action('admin_footer', 'load_custom_price_on_product_edit');

// Функция за деинсталация на плъгина
function photo_price_uninstall() {
  // Изтриване на всички данни, свързани с плъгина, при деинсталация
  delete_option('photo_price');
}

register_uninstall_hook(__FILE__, 'photo_price_uninstall');
