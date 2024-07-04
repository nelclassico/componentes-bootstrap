<?php
/*
Plugin Name: Componentes Bootstrap 
Description: Um plugin para criar Galerias Simples ou galerias de Cards.
Version: 2.1
Author: Emanoel de Oliveira
*/

function comp_bootstrap_scripts() {
    // Enfileirar Bootstrap CSS somente se ainda não estiver enfileirado
    if (!wp_style_is('bootstrap_css', 'enqueued')) {
        wp_enqueue_style('bootstrap', plugins_url('assets/bootstrap/css/bootstrap.min.css', __FILE__));
    }

    // Enfileirar Bootstrap JavaScript somente se ainda não estiver enfileirado
    if (!wp_script_is('bootstrap_js', 'enqueued')) {
        wp_enqueue_script('bootstrap', plugins_url('assets/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'), '5.3.2', true);
    }

    // Enfileirar seu CSS personalizado somente se ainda não estiver enfileirado
    if (!wp_style_is('comp-plugin-css', 'enqueued')) {
        wp_enqueue_style('comp-plugin-css', plugins_url('assets/css/estilo-galeria.css', __FILE__));
    }

    // Enfileirar Lightbox CSS somente se ainda não estiver enfileirado
    if (!wp_style_is('lightbox-css', 'enqueued')) {
        wp_enqueue_style('lightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css');
    }

    // Enfileirar Lightbox JavaScript somente se ainda não estiver enfileirado
    if (!wp_script_is('lightbox-js', 'enqueued')) {
        wp_enqueue_script('lightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), '2.11.3', true);
    }

    // Enfileirar seus scripts personalizados somente se ainda não estiverem enfileirados
    if (!wp_script_is('timeline-scripts', 'enqueued')) {
        wp_enqueue_script('timeline-scripts', plugins_url('assets/js/scripts.js', __FILE__), array('jquery'), '1.0', true);
    }
}

add_action('wp_enqueue_scripts', 'comp_bootstrap_scripts');

function enqueue_metabox_styles() {
    $metabox_styles_path = get_template_directory() . '/assets/css/metabox-styles.css';

    // Verificar se o arquivo CSS existe e ainda não está enfileirado
    if (file_exists($metabox_styles_path) && !wp_style_is('metabox-styles', 'enqueued')) {
        wp_enqueue_style('metabox-styles', get_template_directory_uri() . '/assets/css/metabox-styles.css', array(), '1.0.0', 'all');
    } else {
        // Adicione algum tipo de mensagem de erro ou log para identificar o problema
        error_log('Erro: O arquivo metabox-styles.css não foi encontrado em ' . $metabox_styles_path);
    }
}

add_action('admin_enqueue_scripts', 'enqueue_metabox_styles');

add_action('rwmb_enqueue_scripts', function() {
    if (!wp_style_is('custom-meta-box-style', 'enqueued')) {
        wp_enqueue_style('custom-meta-box-style', get_stylesheet_directory_uri() . '/assets/css/metabox-styles.css');
    }
});


function componentes_bootstrap() {
    // Adiciona o menu principal "Componentes Bootstrap"
    add_menu_page(
        'Componentes Bootstrap', // Título da página
        'Componentes Bootstrap', // Título do menu
        'manage_options', // Capacidade necessária para ver este menu
        'componentes_bootstrap_menu', // Slug do menu
        'componentes_bootstrap_main_page', // Função que mostra o conteúdo da página do menu
        'dashicons-calendar-alt', // Ícone
        20 // Posição no menu
    );

    // Submenu para Card Simples
    add_submenu_page(
        'componentes_bootstrap_menu', // Slug do menu pai
        'GCard Simples', // Título da página
        'GCard Simples', // Título do menu
        'manage_options', // Capacidade necessária
        'edit.php?post_type=gcard-simples' // Slug do Custom Post Type
    );

    // Adicione outros submenus da mesma forma
    add_submenu_page(
        'componentes_bootstrap_menu',
        'NGalerias',
        'NGalerias',
        'manage_options',
        'edit.php?post_type=ngalerias' // Certifique-se de que 'ngalerias' é o slug correto do Custom Post Type
    );

    add_submenu_page(
        'componentes_bootstrap_menu',
        'Linha do Tempo',
        'Linha do Tempo',
        'manage_options',
        'edit.php?post_type=linha-tempo' // Certifique-se de que 'linha-do-tempo' é o slug correto do Custom Post Type
    );
}

add_action('admin_menu', 'componentes_bootstrap');

function componentes_bootstrap_main_page() {
    echo '<h1>Bem-vindo aos Componentes Bootstrap</h1>';
    echo '<p>Selecione uma opção do menu para começar.</p>';
}



// Registrar o Custom Post Type "Card Simples"
function registrar_post_type_gcard_simples() {
    $labels = array(
        'name' => 'GCard Simples',
        'singular_name' => 'Item da GCard',        
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-book',
        'supports' => array('title', 'thumbnail'), // Adicione suporte para campos personalizados.
        //'menu_position'       => 5,  Mesma posição do menu
        'show_in_menu' => false, // Não mostra o Custom Post Type diretamente no menu admin
    );

    register_post_type('gcard-simples', $args);
}

add_action('init', 'registrar_post_type_gcard_simples');

// Adicionar metabox para campos personalizados
function adicionar_metabox_gcard_simples() {
    // Adicione um campo de título
    add_meta_box(
        'metabox-gcard-simples',
        'Título da GCard',
        'renderizar_metabox_gcard_simples',
        'gcard-simples',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'adicionar_metabox_gcard_simples');

// Função para renderizar o metabox
function renderizar_metabox_gcard_simples($post) {
    $post_id = $post->ID;
    $shortcode = get_post_meta($post_id, 'gcard-simples', true);

    if (empty($shortcode)) {
        $shortcode = '[gcard-simples id="' . $post_id . '"]';
        update_post_meta($post_id, 'gcard-simples', $shortcode);
    }

    echo '<p>Shortcode:</p>';
    echo '<input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" class="large-text" />';

    echo '<p>Título do card:</p>';
    echo '<input type="text" name="titulo" value="' . esc_attr(get_post_meta($post->ID, 'titulo', true)) . '" class="large-text" />';

    // Adicione o campo de seleção para o número de colunas
        echo '<p>Número de colunas:</p>';
        echo '<select name="num_colunas">';
        echo '<option value="col-md-2" ' . (esc_attr(get_post_meta($post->ID, 'num_colunas', true)) === 'col-md-2' ? 'selected' : '') . '>5 colunas</option>';
        echo '<option value="col-md-3" ' . (esc_attr(get_post_meta($post->ID, 'num_colunas', true)) === 'col-md-3' ? 'selected' : '') . '>4 colunas</option>';
        echo '<option value="col-md-4" ' . (esc_attr(get_post_meta($post->ID, 'num_colunas', true)) === 'col-md-4' ? 'selected' : '') . '>3 colunas</option>';
        echo '<option value="col-md-6" ' . (esc_attr(get_post_meta($post->ID, 'num_colunas', true)) === 'col-md-6' ? 'selected' : '') . '>2 colunas</option>';
        // Adicione mais opções conforme necessário
        echo '</select>';

    $conteudos = get_post_meta($post_id, 'conteudos-gcard', true);

    if (empty($conteudos)) {
        $conteudos = array();
    }
    ?>

    <div id="conteudos-container-gcard">
        <?php foreach ($conteudos as $index => $conteudo): ?>
            <div class="conteudo" style="margin-bottom: 10px;">
                <label for="titulo_<?php echo $index; ?>">Título:</label><br>
                <input type="text" name="conteudos-gcard[<?php echo $index; ?>][titulo]" value="<?php echo esc_attr($conteudo['titulo']); ?>" /><br>

                <label for="data_<?php echo $index; ?>">Data:</label><br>
                <input type="text" name="conteudos-gcard[<?php echo $index; ?>][data]" id="data_<?php echo $index; ?>" value="<?php echo esc_attr($conteudo['data']); ?>" /><br>

                <label for="imagem_<?php echo $index; ?>">Imagem:</label><br>
                <div class="custom-media-uploader">
                    <button type="button" class="button upload-image-button">Selecionar Imagem</button>
                    <img src="<?php echo esc_attr($conteudo['imagem']); ?>" class="preview-image" style="max-width: 30%; height: auto; display: <?php echo empty($conteudo['imagem']) ? 'none' : 'block'; ?>" />
                    <input type="hidden" name="conteudos-gcard[<?php echo $index; ?>][imagem]" id="imagem_<?php echo $index; ?>" value="<?php echo esc_attr($conteudo['imagem']); ?>" class="image-url" />
                </div><br>


                <label for="conteudo_<?php echo $index; ?>">Conteúdo:</label><br>
                <textarea name="conteudos-gcard[<?php echo $index; ?>][conteudo]" class="wp-editor-area" rows="10" cols="50"><?php echo wp_kses_post($conteudo['conteudo']); ?></textarea><br>

                <label for="posicao_<?php echo $index; ?>">Posição:</label><br>
                <input type="number" name="conteudos-gcard[<?php echo $index; ?>][posicao]" value="<?php echo esc_attr($conteudo['posicao']); ?>" />

                <button class="excluir-conteudo button">Excluir</button><br>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>

    <button id="adicionar-conteudo" class="button">Adicionar Conteúdo</button>

    <script>
            jQuery(document).ready(function($) {
                // Função para lidar com o botão de upload de imagem
                $(document).on('click', '.upload-image-button', function(e) {
                    e.preventDefault();

                    var imageField = $(this).siblings('.image-url');
                    var previewImage = $(this).siblings('.preview-image');

                    var imageFrame = wp.media({
                        title: 'Escolher uma imagem',
                        multiple: false,
                        library: { type: 'image' },
                        button: { text: 'Usar imagem selecionada' }
                    });

                    imageFrame.on('select', function() {
                        var attachment = imageFrame.state().get('selection').first().toJSON();
                        imageField.val(attachment.url);
                        previewImage.attr('src', attachment.url).css('display', 'block');
                    });

                    imageFrame.open();
                });

                // Adicionar novo conteúdo
                $('#adicionar-conteudo').on('click', function() {
                    var index = $('#conteudos-container-gcard .conteudo').length;

                    var novoConteudo = `
                        <div class="conteudo" style="margin-bottom: 10px;">
                            <label for="titulo_${index}">Título:</label><br>
                            <input type="text" name="conteudos-gcard[${index}][titulo]" id="titulo_${index}" /><br>

                            <label for="data_${index}">Data:</label><br>
                            <input type="text" name="conteudos-gcard[${index}][data]" id="data_${index}" /><br>

                            <label for="imagem_${index}">Imagem:</label><br>
                            <div class="custom-media-uploader">
                                <button type="button" class="button upload-image-button">Selecionar Imagem</button>
                                <img src="" class="preview-image" style="max-width: 30%; height: auto; display: none;" />
                                <input type="hidden" name="conteudos-gcard[${index}][imagem]" id="imagem_${index}" class="image-url" />
                            </div><br>

                            <label for="conteudo_${index}">Conteúdo:</label><br>
                            <textarea name="conteudos-gcard[${index}][conteudo]" class="wp-editor-area" rows="10" cols="50"></textarea><br>

                            <label for="posicao_${index}">Posição:</label><br>
                            <input type="number" name="conteudos-gcard[${index}][posicao]" value="${index + 1}" />

                            <button class="excluir-conteudo button">Excluir</button><br>
                            <hr>
                        </div>
                    `;

                    $('#conteudos-container-gcard').append(novoConteudo);

                    return false;
                });

                // Excluir conteúdo existente
                $('#conteudos-container-gcard').on('click', '.excluir-conteudo', function() {
                    if (confirm('Tem certeza de que deseja excluir este item?')) {
                        $(this).closest('.conteudo').remove();
                    }
                });
            });

    </script>

    <?php
    echo do_shortcode($shortcode);
}

// Salvar os dados dos campos personalizados
function salvar_metabox_gcard_simples($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['post_type']) && 'gcard-simples' == $_POST['post_type']) {
        // Salvar o valor do número de colunas
        if (isset($_POST['num_colunas'])) {
            $num_colunas = sanitize_text_field($_POST['num_colunas']);
            update_post_meta($post_id, 'num_colunas', $num_colunas);
        }

        if (isset($_POST['conteudos-gcard'])) {
            $conteudos = $_POST['conteudos-gcard'];

            // Verifique se as posições não foram fornecidas e preencha automaticamente
            if (!empty($conteudos) && is_array($conteudos)) {
                foreach ($conteudos as $index => $conteudo) {
                    if (empty($conteudo['posicao'])) {
                        $conteudos[$index]['posicao'] = $index + 1;
                    }

                    /* Inclui o tratamento para o campo 'data'
                    if (!empty($conteudo['data'])) {
                        $conteudos[$index]['data'] = sanitize_text_field($conteudo['data']);
                    } */
                }
            }

            usort($conteudos, function ($a, $b) {
                return intval($a['posicao']) - intval($b['posicao']);
            });

            update_post_meta($post_id, 'conteudos-gcard', $conteudos);
        }

        if (isset($_POST['gcard-simples'])) {
            $shortcode = sanitize_text_field($_POST['gcard-simples']);
            update_post_meta($post_id, 'gcard-simples', $shortcode);
        }
        
        // Atualizar a imagem com o valor do campo hidden
        if (isset($_POST['conteudos-gcard'])) {
            $conteudos = $_POST['conteudos-gcard'];
            foreach ($conteudos as $index => $conteudo) {
                if (isset($conteudo['imagem'])) {
                    update_post_meta($post_id, "conteudos-gcard[{$index}][imagem]", sanitize_text_field($conteudo['imagem']));
                }
            }
        }
    }
}


add_action('save_post', 'salvar_metabox_gcard_simples');



function gcard_simples_shortcode($atts) {
    if (!isset($atts['id'])) {
        return 'ID da publicação não especificado.';
    }

    $post_id = $atts['id'];
    $num_colunas = get_post_meta($post_id, 'num_colunas', true);

    $gcard_items = array();
    $conteudos = get_post_meta($post_id, 'conteudos-gcard', true);

    if ($conteudos && is_array($conteudos)) {
        foreach ($conteudos as $conteudo) {
            $imagem = isset($conteudo['imagem']) ? $conteudo['imagem'] : '';
            $title = isset($conteudo['titulo']) ? $conteudo['titulo'] : '';
            $data = isset($conteudo['data']) ? $conteudo['data'] : '';
            $content = isset($conteudo['conteudo']) ? $conteudo['conteudo'] : '';

            if ($imagem && $title && $content) {
                $gcard_items[] = array(
                    'imagem' => $imagem,
                    'content' => $content,
                    'title' => $title,
                    'data' => $data,
                );
            }
        }
    }

    ob_start();
    ?>
    <div id="timelineGallery" class="row mt-5">
    <?php
    $item_count = 0;
    foreach ($gcard_items as $item):
    ?>
        <div class="<?php echo esc_attr($num_colunas); ?>">
            <div class="card mb-4 shadow card-altura">  
                <a href="#" class="card-link" data-bs-toggle="modal" data-bs-target="#modal_<?php echo esc_attr($post_id . '_' . $item_count); ?>">              
                    <img src="<?php echo esc_url($item['imagem']); ?>" alt="Imagem do item" class="img-fluid card-img-top">
                </a>
                <div class="card-body p-3">
                    <h5 class="card-text text-center"><span><?php echo esc_html($item['title']); ?></span></h5>
                    <p class="card-date text-center"><?php echo esc_html(($item['data'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modal_<?php echo esc_attr($post_id . '_' . $item_count); ?>" tabindex="-1" role="dialog" aria-labelledby="modal_<?php echo esc_attr($post_id . '_' . $item_count); ?>_label" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_<?php echo esc_attr($post_id . '_' . $item_count); ?>_label"><?php echo esc_html($item['title']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo wpautop($item['content']); ?>
                    </div>
                </div>
            </div>
        </div>

    <?php
    $item_count++;
    endforeach;
    ?>
</div>

<!-- Adicione o seguinte script no rodapé da página para garantir que o Bootstrap JavaScript esteja carregado corretamente -->
<script>
    jQuery(document).ready(function($) {
        $('#timelineGallery .card-link').click(function(event) {
            event.preventDefault();
            $($(this).attr('data-target')).modal();
        });
    });
</script>

    <?php
    return ob_get_clean();
}

add_shortcode('gcard-simples', 'gcard_simples_shortcode');

/* *****  
*
*

Registrar um post type para ngalerias 

*
*
****/
function registrar_post_type_ngalerias() {
    $labels = array(
        'name'               => 'ngalerias',
        'singular_name'      => 'ngaleria',        
        'add_new'            => 'Adicionar Nova ngaleria',
        'add_new_item'       => 'Adicionar Nova ngaleria',
        'edit_item'          => 'Editar ngaleria',
        'new_item'           => 'Nova ngaleria',
        'view_item'          => 'Ver ngaleria',
        'search_items'       => 'Pesquisar ngalerias',
        'not_found'          => 'Nenhuma ngaleria encontrada',
        'not_found_in_trash' => 'Nenhuma ngaleria encontrada na lixeira',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'menu_icon'           => 'dashicons-format-gallery',
        'query_var'           => true,
        'rewrite'             => array('slug' => 'ngalerias'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'supports'            => array('title', 'thumbnail'),
        // 'menu_position'       => 5,  Mesma posição do menu
    );

    register_post_type('ngalerias', $args);
}

add_action('init', 'registrar_post_type_ngalerias');



// Registrar uma taxonomia para categorias de ngalerias
function registrar_taxonomia_categorias_ngaleria() {
    $labels = array(
        'name'              => 'Categorias de ngaleria',
        'singular_name'     => 'Categoria de ngaleria',
        'search_items'      => 'Pesquisar Categorias de ngaleria',
        'all_items'         => 'Todas as Categorias de ngaleria',
        'parent_item'       => 'Categoria de ngaleria Pai',
        'parent_item_colon' => 'Categoria de ngaleria Pai:',
        'edit_item'         => 'Editar Categoria de ngaleria',
        'update_item'       => 'Atualizar Categoria de ngaleria',
        'add_new_item'      => 'Adicionar Nova Categoria de ngaleria',
        'new_item_name'     => 'Nome da Nova Categoria de ngaleria',
        'menu_name'         => 'Categorias de ngaleria',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'categoria-ngaleria'),
    );

    register_taxonomy('categoria-ngaleria', array('ngalerias'), $args);
}

add_action('init', 'registrar_taxonomia_categorias_ngaleria');



// Adicionar metabox para campos personalizados
function adicionar_metabox_ngaleria() {
    add_meta_box(
        'metabox-ngaleria',
        'Imagens da ngaleria',
        'renderizar_metabox_ngaleria',
        'ngalerias',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes_ngalerias', 'adicionar_metabox_ngaleria');


// Função para renderizar o campo de imagens da ngaleria
function renderizar_metabox_ngaleria($post) {
    // Adicione o campo de seleção para o número de colunas
    echo '<p>Número de colunas:</p>';
    echo '<select name="num_colunas">';
    $current_colunas = get_post_meta($post->ID, 'num_colunas', true);

    $options = array(
        'col-md-2' => '5 colunas',
        'col-md-3' => '4 colunas',
        'col-md-4' => '3 colunas',
        'col-md-6' => '2 colunas',
        // Adicione mais opções conforme necessário
    );

    foreach ($options as $value => $label) {
        $selected = ($current_colunas === $value) ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';

    $post_id = $post->ID;

    echo '<p>Shortcode:</p>';
    $shortcode = '[ngalerias id="' . $post_id . '"]';
    echo '<input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" class="large-text" />';

    // Renderize o campo nonce para segurança
    wp_nonce_field(basename(__FILE__), 'metabox_ngaleria_nonce');

    // Recupere as imagens da ngaleria, se existirem
    $imagens = get_post_meta($post_id, 'imagens-ngaleria', true);
    ?>
    <div id="imagens-container-ngaleria">
        <button class="upload-imagens button button_slide slide_left">Selecionar Imagens</button>
        <div class="image-preview">
            <?php
            if (!empty($imagens)) {
                $image_urls = explode(', ', $imagens);
                foreach ($image_urls as $image_url) {
                    echo '<div class="image-item">';
                    echo '<img src="' . esc_url($image_url) . '" alt="Imagem" style="max-width: 100%;" />';
                    echo '<input type="hidden" name="imagens-ngaleria[]" value="' . esc_url($image_url) . '" />';
                    echo '<button class="remove-image button">Remover</button>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#imagens-container-ngaleria').on('click', '.upload-imagens', function(e) {
                e.preventDefault();
                var target = $(this).siblings('.image-preview');
                var frame = wp.media({
                    title: 'Selecione ou faça upload de imagens',
                    multiple: true,
                    library: {
                        type: 'image',
                    },
                    button: {
                        text: 'Selecionar Imagens',
                    },
                });
                frame.on('select', function() {
                    var selection = frame.state().get('selection');
                    selection.each(function(attachment) {
                        var imageUrl = attachment.attributes.url;
                        var imageItem = '<div class="image-item">';
                        imageItem += '<img src="' + imageUrl + '" alt="Imagem" style="max-width: 100%;" />';
                        imageItem += '<input type="hidden" name="imagens-ngaleria[]" value="' + imageUrl + '" />';
                        imageItem += '<button class="remove-image button">Remover</button>';
                        imageItem += '</div>';
                        target.append(imageItem);
                    });
                });
                frame.open();
            });

            // Remover imagem
            $('#imagens-container-ngaleria').on('click', '.remove-image', function(e) {
                e.preventDefault();
                $(this).parent('.image-item').remove();
            });
        });
    </script>
    <?php
}

// Salvar as imagens da ngaleria
function salvar_metabox_ngaleria($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['post_type']) && 'ngalerias' == $_POST['post_type']) {

        // Salvar o valor do número de colunas
        if (isset($_POST['num_colunas'])) {
            $num_colunas = sanitize_text_field($_POST['num_colunas']);
            update_post_meta($post_id, 'num_colunas', $num_colunas);
        }

        // Verifique o nonce
        if (!isset($_POST['metabox_ngaleria_nonce']) || !wp_verify_nonce($_POST['metabox_ngaleria_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        // Remova as imagens existentes antes de salvar as novas
        delete_post_meta($post_id, 'imagens-ngaleria');

        // Salve as imagens da ngaleria
        if (isset($_POST['imagens-ngaleria'])) {
            $imagens = array_map('esc_url', $_POST['imagens-ngaleria']);
            update_post_meta($post_id, 'imagens-ngaleria', implode(', ', $imagens));
        }
    }
}


add_action('save_post', 'salvar_metabox_ngaleria');


function ngalerias_shortcode($atts) {
    $atts = shortcode_atts(array(
        'categoria' => '',
        'posts_per_page' => -1,
        'id' => '',
    ), $atts);

    $args = array(
        'post_type' => 'ngalerias',
        'posts_per_page' => $atts['posts_per_page'],
    );

    if (!empty($atts['categoria'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categoria-ngaleria',
                'field' => 'slug',
                'terms' => $atts['categoria'],
            ),
        );
    }

    $ngalerias = new WP_Query($args);

    ob_start();
    if ($ngalerias->have_posts()) {
        while ($ngalerias->have_posts()) {
            $ngalerias->the_post();
            $post_id = get_the_ID();

            $num_colunas = get_post_meta($post_id, 'num_colunas', true); // Obtenha o valor das colunas do metabox

            if ($ngalerias->post_count > 0 && $atts['id'] == $post_id) {
                $imagens = get_post_meta($post_id, 'imagens-ngaleria', true);
                $image_urls = explode(', ', $imagens);

                if (!empty($image_urls)) {
                    echo '<div class="row"> ';
                    foreach ($image_urls as $image_url) {
                        echo '<div class="' . $num_colunas . ' altura-imagens" style="background-image: url(' . $image_url .')">';
                        echo '<a href="' . $image_url . '" data-lightbox="ngaleria">';
                        echo '<img src="' . $image_url . '" alt="' . get_post_meta($post_id, '_wp_attachment_image_alt', true) . '">';
                        echo '</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo 'Nenhuma ngaleria encontrada.';
            }
        }
    } else {
        echo 'Nenhuma ngaleria encontrada.';
    }
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('ngalerias', 'ngalerias_shortcode');




/* Registrar o Custom Post Type "Linha do Tempo" */
function registrar_post_type_linha() {
    $labels = array(
        'name' => 'Linha do Tempo',
        'singular_name' => 'Item de Linha do Tempo',
        //'menu_name' => 'Linha do Tempo',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'rewrite' => false,
        'show_in_menu' => false, // Não mostra o Custom Post Type diretamente no menu admin
        'menu_icon' => 'dashicons-image-flip-horizontal',
        'supports' => array('title', 'thumbnail'), // Adicione suporte para campos personalizados.
        //'menu_position'       =>    5, // Mesma posição do menu
    );

    register_post_type('linha-tempo', $args);
}

add_action('init', 'registrar_post_type_linha');

// Adicionar metabox para campos personalizados
function adicionar_metabox_timeline() {
    add_meta_box(
        'metabox-timeline', // ID único do metabox
        'Campos Personalizados', // Título do metabox
        'renderizar_metabox_timeline', // Função de renderização do metabox
        'linha-tempo', // Nome do tipo de post
        'normal', // Contexto do metabox (normal, lado, avançado)
        'high' // Prioridade do metabox (high, core, default, low)
    );
}

add_action('add_meta_boxes', 'adicionar_metabox_timeline');

function renderizar_metabox_timeline($post) {
    // Recupere o ID do post atual
    $post_id = $post->ID;

    // Recupere o valor do shortcode, se existir
    $shortcode = get_post_meta($post_id, 'linha_do_tempo', true);

    // Se não houver um shortcode, crie um com base no ID do post
    if (empty($shortcode)) {
        $shortcode = '[linha_do_tempo id="' . $post_id . '"]';
        update_post_meta($post_id, 'linha_do_tempo', $shortcode);
    }

    // Exiba o shortcode
    echo '<p>Shortcode:</p>';
    echo '<input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" class="large-text" />';

    // Recupere os valores dos campos personalizados, se eles existirem
    $itens_timeline = get_post_meta($post->ID, 'itens_timeline', true);

    // Inicialize com um array vazio se não houver conteúdo
    if (empty($itens_timeline)) {
        $itens_timeline = array();
    }
    ?>

    <div id="conteudos-container">
        <?php foreach ($itens_timeline as $index => $item_timeline): ?>
            <div class="conteudo" style="margin-bottom: 10px;">
                <label for="titulo_<?php echo $index; ?>">Data:</label><br>
                <input type="text" name="itens_timeline[<?php echo $index; ?>][titulo]" value="<?php echo esc_attr($item_timeline['titulo']); ?>" /><br>

                <label for="data_<?php echo $index; ?>">Título:</label><br>
                <input type="text" name="itens_timeline[<?php echo $index; ?>][data]" value="<?php echo esc_attr($item_timeline['data']); ?>" /><br>

                <label for="conteudo_<?php echo $index; ?>">Conteúdo:</label><br>
                <?php
                $content = isset($item_timeline['conteudo']) ? $item_timeline['conteudo'] : '';
                wp_editor($content, 'conteudo_' . $index, array(
                    'textarea_name' => 'itens_timeline[' . $index . '][conteudo]',
                    'editor_height' => 200, // Altura do editor em pixels
                ));
                ?><br>

                <label for="posicao_<?php echo $index; ?>">Posição:</label><br>
                <input type="number" name="itens_timeline[<?php echo $index; ?>][posicao]" value="<?php echo esc_attr($item_timeline['posicao']); ?>" />

                <!-- Adicione um botão Excluir -->
                <button class="excluir-conteudo button">Excluir</button><br>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>

    <button id="adicionar-conteudo" class="button">Adicionar Conteúdo</button>

    <script>
        jQuery(document).ready(function ($) {
            // Adicionar novo conteúdo
            $('#adicionar-conteudo').on('click', function () {
                var index = $('#conteudos-container .conteudo').length;

                var novoConteudo = `
                    <div class="conteudo" style="margin-bottom: 10px;">
                        <label for="titulo_${index}">Data:</label><br>
                        <input type="text" name="itens_timeline[${index}][titulo]" id="titulo_${index}" /><br>

                        <label for="data_${index}">Título:</label><br>
                        <input type="text" name="itens_timeline[${index}][data]" id="data_${index}" /><br>

                        <label for="conteudo_${index}">Conteúdo:</label><br>
                        <div id="editor-container-${index}"></div>
                        <textarea name="itens_timeline[${index}][conteudo]" id="conteudo_${index}" style="display: none;"></textarea><br>

                        <label for="posicao_${index}">Posição:</label><br>
                        <input type="number" name="itens_timeline[${index}][posicao]" />

                        <!-- Adicione um botão Excluir -->
                        <button class="excluir-conteudo button">Excluir</button>
                        <hr>
                    </div>
                `;

                // Anexe o novo conteúdo
                $('#conteudos-container').append(novoConteudo);

                // Inicialize o editor visual do WordPress
                var editorId = 'conteudo_' + index;
                tinymce.init({
                    selector: `#editor-container-${index}`,
                    tinymce4: {
                        skin: 'wpadmin',
                    },
                    setup: function (editor) {
                        editor.on('change', function () {
                            $('#' + editorId).val(editor.getContent());
                        });
                    },
                });

                return false;
            });

            // Excluir conteúdo existente
            $('#conteudos-container').on('click', '.excluir-conteudo', function () {
                if (confirm('Tem certeza de que deseja excluir este item da linha do tempo?')) {
                    var editorId = $(this).closest('.conteudo').find('.wp-editor-area').attr('id');
                    tinymce.get(editorId).remove(); // Remover o editor TinyMCE
                    $(this).closest('.conteudo').remove();
                }
            });
        });
    </script>

    <?php
    // Adicione o shortcode ao final do conteúdo do metabox
    echo do_shortcode($shortcode);
}



// Salvar os dados dos campos personalizados
function salvar_metabox_timeline($post_id) {
    // Verificar permissões
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if ($post_id && isset($_POST['post_type']) && 'linha-tempo' == $_POST['post_type']) {
        $itens_timeline = isset($_POST['itens_timeline']) ? $_POST['itens_timeline'] : array();

        // Reorganizar os itens com base na posição
        usort($itens_timeline, function ($a, $b) {
            return intval($a['posicao']) - intval($b['posicao']);
        });

        // Limpar o conteúdo dos itens da linha do tempo
        foreach ($itens_timeline as &$item_timeline) {
            // Obter o conteúdo do TinyMCE do campo
            $editor_id = 'conteudo_' . $item_timeline['posicao'];
            if (isset($_POST[$editor_id])) {
                $item_timeline['conteudo'] = wp_kses_post(wp_unslash($_POST[$editor_id]));
            }
        }

        update_post_meta($post_id, 'itens_timeline', $itens_timeline);

        // Salvar o valor do campo shortcode
        if (isset($_POST['linha_do_tempo'])) {
            $shortcode = sanitize_text_field($_POST['linha_do_tempo']);
            update_post_meta($post_id, 'linha_do_tempo', $shortcode);
        }
    }
}


add_action('save_post', 'salvar_metabox_timeline');

/* Função para renderizar o shortcode da linha do tempo
function linha_do_tempo_shortcode($atts) {
    // Certifique-se de que $post_id está definido
    if (!isset($atts['id'])) {
        return 'ID da publicação não especificado.';
    }

    $post_id = $atts['id'];

    // Recupere os itens da linha do tempo diretamente da postagem atual
    $timeline_items = array();

    $itens_timeline = get_post_meta($post_id, 'itens_timeline', true);

    if ($itens_timeline && is_array($itens_timeline)) {
        foreach ($itens_timeline as $item_timeline) {
            $date = isset($item_timeline['data']) ? $item_timeline['data'] : '';
            $title = isset($item_timeline['titulo']) ? $item_timeline['titulo'] : '';
            $content = isset($item_timeline['conteudo']) ? $item_timeline['conteudo'] : '';

            if ($date && $title && $content) {
                $timeline_items[] = array(
                    'date' => $date,
                    'content' => $content,
                    'title' => $title,
                );
            }
        }
    }

    // Agora, você pode usar os itens da linha do tempo para criar o conteúdo do shortcode
    ob_start();
    ?>
    <div id="timelineCarousel" class="carousel slide mt-5" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="timeline-horizontal"></div>
                <?php
                $item_count = 0;
                for ($i = 0; $i < count($timeline_items); $i += 5):
                    $active_class = $item_count === 0 ? 'active' : '';
                ?>
                    <div class="carousel-item <?php echo $active_class; ?>">
                        <div class="timeline-container">
                            <?php for ($j = $i; $j < $i + 5; $j++): ?>
                                <?php if (isset($timeline_items[$j])): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-title year">
                                            <h5 class="timeline-title-h"><span><?php echo esc_html($timeline_items[$j]['title']); ?></span></h5>
                                        </div>
                                        <div class="timeline-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="timeline-line-right"></div>
                                                    <div class="timeline-date">
                                                        <h5><?php echo esc_html($timeline_items[$j]['date']); ?></h5>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <?php echo wpautop($timeline_items[$j]['content']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php
                    $item_count++;
                endfor;
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#timelineCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#timelineCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
            </div>
    <?php
    return ob_get_clean();
}

add_shortcode('linha_do_tempo', 'linha_do_tempo_shortcode');
*/
// Função para renderizar o shortcode da linha do tempo
function linha_do_tempo_shortcode($atts) {
    // Certifique-se de que $post_id está definido
    if (!isset($atts['id'])) {
        return 'ID da publicação não especificado.';
    }

    $post_id = $atts['id'];

    // Recupere os itens da linha do tempo diretamente da postagem atual
    $timeline_items = array();

    $itens_timeline = get_post_meta($post_id, 'itens_timeline', true);

    if ($itens_timeline && is_array($itens_timeline)) {
        foreach ($itens_timeline as $item_timeline) {
            $date = isset($item_timeline['data']) ? $item_timeline['data'] : '';
            $title = isset($item_timeline['titulo']) ? $item_timeline['titulo'] : '';
            $content = isset($item_timeline['conteudo']) ? $item_timeline['conteudo'] : '';

            if ($date && $title && $content) {
                $timeline_items[] = array(
                    'date' => $date,
                    'content' => $content,
                    'title' => $title,
                );
            }
        }
    }

    // Ordenar os itens da linha do tempo pela data, do mais antigo para o mais recente
    usort($timeline_items, function ($a, $b) {
        $date_a = strtotime($a['date']);
        $date_b = strtotime($b['date']);
        return $date_a - $date_b;
    });

    // Agora, você pode usar os itens da linha do tempo para criar o conteúdo do shortcode
    ob_start();
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <button id="fullscreenBtn">Expandir Tela Cheia</button>
                <?php
                // Obtenha a URL da miniatura em destaque
                $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
                ?>
                <div class="container-timeline" style="background: url('<?php echo esc_url($thumbnail_url); ?>');" id="myContent">
                    <ul id="timeline" class="timeline timeline-horizontal">
                        <?php foreach ($timeline_items as $index => $item): ?>
                            <li id="timeline-item-<?php echo $index; ?>" class="timeline-item">
                                <div class="timeline-badge info">
                                    <p class="timeline-the-title"><?php echo esc_html($item['title']); ?></p>
                                    <i class="glyphicon glyphicon-check"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-the-title"><?php echo esc_html($item['date']); ?></h4>
                                    </div>
                                    <div class="timeline-body">
                                        <?php
                                        // Verifica se o conteúdo possui imagens
                                        $content_with_lightbox = preg_replace_callback('/<a\s[^>]*?href=[\'"]([^\'"]+)[\'"][^>]*><img[^>]+><\/a>/i', function ($match) {
                                            // Adiciona o atributo data-lightbox ao link
                                            return preg_replace('/<a/i', '<a data-lightbox="'.esc_attr($match[1]).'"', $match[0]);
                                        }, wpautop($item['content']));

                                        // Exibe o conteúdo com os links ajustados
                                        echo $content_with_lightbox;
                                        ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex flex-wrap justify-content-end botoes-linha">
                        <div id="carouselLine" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <?php
                                    $chunked_items = array_chunk($timeline_items, 30);
                                    foreach ($chunked_items as $chunk_index => $chunk): ?>
                                        <div class="carousel-item <?php echo $chunk_index === 0 ? 'active' : ''; ?>">
                                            <?php foreach ($chunk as $index => $item): ?>
                                                <a href="#timeline-item-<?php echo $index + ($chunk_index * 30); ?>" class="timeline-link text-dark scroll-link m-1 <?php echo $index % 2 == 0 ? 'above' : 'below'; ?>">
                                                    <?php echo $item['title']; ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselLine" data-bs-slide="prev">
                               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                               <span class="visually-hidden">Previous</span>
                             </button>
                             <button class="carousel-control-next" type="button" data-bs-target="#carouselLine" data-bs-slide="next">
                               <span class="carousel-control-next-icon" aria-hidden="true"></span>
                               <span class="visually-hidden">Next</span>
                             </button>
                        </div>
                    </div>  
                </div>
            </div>            
        </div>
    </div>

 
    <?php
    return ob_get_clean();
}

add_shortcode('linha_do_tempo', 'linha_do_tempo_shortcode');

