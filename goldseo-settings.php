<?php
if ( ! defined( 'ABSPATH' ) ) {
    die('direct access disabled');
}
?>

<div class="wrap">

    <div style="float: right">Version: <?php echo $version ?></div>
    <h1>GoldSEO</h1>
    <hr>
    <br>

    <h2 class="nav-tab-wrapper wp-clearfix" id="goldTabNav">
        <a href="#" class="nav-tab nav-tab-active" data-show="goldContentTabGeneral">General settings</a>
        <a href="#" class="nav-tab" data-show="goldContentTabCode" style="display: none">Integration code</a>
    </h2>

    <form method="post">

        <input type="hidden" name="soldseoSaveSettings" value="1">

        <div class="goldContentTab" id="goldContentTabGeneral">



            <table class="wp-list-table widefat pages form-table" cellspacing="0">

                <tr>
                    <td>
                        <b>Meta  description tag settings</b>
                    </td>
                    <td>

                    </td>
                </tr>

                <tr>
                    <td>
                        Strip shortcode method
                    </td>
                    <td>
                        <select name="stripmethod">
                            <option value="nostrip">No strip</option>
                            <option value="stripall" <?php if (isset($settings['stripmethod']) && $settings['stripmethod']==='stripall') echo 'selected'; ?> >Strip all</option>
                            <option value="stripkeep" <?php if (isset($settings['stripmethod']) && $settings['stripmethod']==='stripkeep') echo 'selected'; ?>>Strip but keep content in between</option>

                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        Use content if excerpt id empty
                    </td>
                    <td>
                        <select name="usecontent">
                            <option value="yes">Yes</option>
                            <option value="no" <?php if (isset($settings['usecontent']) && $settings['usecontent']==='no') echo 'selected'; ?>>No</option>
                        </select>
                        &nbsp; &nbsp; &nbsp;
                        Max. content size:
                        <input type="number" name="maxcontentsize" value="<?php echo $settings['maxcontentsize']; ?>" size="4">
                    </td>
                </tr>

                <tr>
                    <td>
                        <b>The Open Graph</b> <small><a href="http://ogp.me/" target="_blank">docs</a></small>
                    </td>
                    <td>

                    </td>
                </tr>

                <tr>
                    <td>
                        <label title="The title of your object as it should appear within the graph">
                        Show og:title
                        </label>
                    </td>
                    <td>
                        <select name="ogtitle">
                            <option value="yes">Yes</option>
                            <option value="no" <?php if (isset($settings['ogtitle']) && $settings['ogtitle']==='no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>


                <tr>
                    <td>
                        <label title="A one to two sentence description of your object.">
                            Show og:description
                        </label>
                    </td>
                    <td>
                        <select name="ogdescription">
                            <option value="yes">Yes</option>
                            <option value="no" <?php if (isset($settings['ogdescription']) && $settings['ogdescription']==='no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>


                <tr>
                    <td>
                        <label title="The type of your object. Depending on the type you specify, other properties may also be required.">
                            Show og:type
                        </label>
                    </td>
                    <td>
                        <input type="text" name="ogtype" placeholder="website or article or blog" value="<?php if (isset($settings['ogtype'])) echo $settings['ogtype']; ?>" >
                    </td>
                </tr>

                <tr>
                    <td>
                        <label title="The canonical URL of your object that will be used as its permanent ID in the graph">
                            Show og:url
                        </label>
                    </td>
                    <td>
                        <select name="ogurl">
                            <option value="yes">Yes</option>
                            <option value="no" <?php if (isset($settings['ogurl']) && $settings['ogurl']==='no') echo 'selected'; ?>>No</option>
                        </select>

                    </td>
                </tr>

                <tr>
                    <td>
                        <label title="The locale these tags are marked up in.">
                            Show og:locate
                        </label>
                    </td>
                    <td>
                        <select name="oglocale">
                            <?php
                            foreach ($locates as $locate) {
                                $sel = ($settings['oglocale'] == $locate) ? 'selected' : '';
                                echo '<option value="'.$locate.'" '.$sel.'>'.$locate.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>


                <tr>
                    <td>
                        <label title="An image URL which should represent your object within the graph.">
                            Show og:image
                        </label>
                    </td>
                    <td>
                        <input type="text" name="ogimage" placeholder="An full image URL" value="<?php if (isset($settings['ogimage'])) echo $settings['ogimage']; ?>" style="width: 99%">
                    </td>
                </tr>


            </table>



        </div>

        <div class="goldContentTab" id="goldContentTabCode" style="background-color: #fff; display: none;">

            <table class="wp-list-table widefat pages" cellspacing="0">

                <tr>
                    <td>
                        <b>Integration code (JavaScript)</b>
                    </td>
                    <td>

                    </td>
                </tr>

            </table>

        </div>

        <?php submit_button(); ?>

    </form>

</div>

<?php

//print_r(ResourceBundle::getLocales(''));

//file_put_contents('/media/data/code/php/skansen/new1/wordpress/wp-content/cache/locates.json', json_encode(ResourceBundle::getLocales('')));

//var_dump($locates);

var_dump($settings);

var_dump($_POST);
?>

<script>

    jQuery(document).ready(function($){

        jQuery('#goldTabNav a').click(function() {
            jQuery('.goldContentTab').hide();
            jQuery('#goldTabNav a').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('#'+jQuery(this).data('show')).show();
        });
    });

</script>