<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap dots-forms">
    <h1><?php esc_html_e('Ticket Form Management', 'dream-online-ticket-selling'); ?></h1>
    <p><?php esc_html_e('Customize the fields that appear in the ticket purchase form.', 'dream-online-ticket-selling'); ?></p>
    
    <div class="dots-forms-grid">
        <div class="dots-forms-main">
            <h2><?php esc_html_e('Form Fields', 'dream-online-ticket-selling'); ?></h2>
            <p><?php esc_html_e('Drag and drop to reorder fields.', 'dream-online-ticket-selling'); ?></p>
            
            <div id="dots-fields-list" class="dots-sortable-fields">
                <?php if (!empty($fields)): ?>
                    <?php foreach ($fields as $field): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                        <div class="dots-field-item" data-field-id="<?php echo esc_attr($field->id); ?>">
                            <span class="dashicons dashicons-menu"></span>
                            <div class="dots-field-info">
                                <strong><?php echo esc_html($field->field_label); ?></strong>
                                <span class="dots-field-type"><?php echo esc_html($field->field_type); ?></span>
                                <?php if ($field->is_required): ?>
                                    <span class="dots-field-required"><?php esc_html_e('Required', 'dream-online-ticket-selling'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="dots-field-actions">
                                <button type="button" class="button dots-edit-field" data-field-id="<?php echo esc_attr($field->id); ?>">
                                    <?php esc_html_e('Edit', 'dream-online-ticket-selling'); ?>
                                </button>
                                <button type="button" class="button dots-delete-field" data-field-id="<?php echo esc_attr($field->id); ?>">
                                    <?php esc_html_e('Delete', 'dream-online-ticket-selling'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php esc_html_e('No custom fields yet. Add your first field below.', 'dream-online-ticket-selling'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dots-forms-sidebar">
            <h2><?php esc_html_e('Add New Field', 'dream-online-ticket-selling'); ?></h2>
            <form id="dots-field-form">
                <table class="form-table">
                    <tr>
                        <th><label for="field_name"><?php esc_html_e('Field Name', 'dream-online-ticket-selling'); ?></label></th>
                        <td>
                            <input type="text" id="field_name" name="field_name" class="regular-text" required>
                            <p class="description"><?php esc_html_e('Internal field name (lowercase, no spaces)', 'dream-online-ticket-selling'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="field_label"><?php esc_html_e('Field Label', 'dream-online-ticket-selling'); ?></label></th>
                        <td><input type="text" id="field_label" name="field_label" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="field_type"><?php esc_html_e('Field Type', 'dream-online-ticket-selling'); ?></label></th>
                        <td>
                            <select id="field_type" name="field_type" required>
                                <option value="text"><?php esc_html_e('Text', 'dream-online-ticket-selling'); ?></option>
                                <option value="email"><?php esc_html_e('Email', 'dream-online-ticket-selling'); ?></option>
                                <option value="tel"><?php esc_html_e('Phone', 'dream-online-ticket-selling'); ?></option>
                                <option value="textarea"><?php esc_html_e('Textarea', 'dream-online-ticket-selling'); ?></option>
                                <option value="select"><?php esc_html_e('Dropdown', 'dream-online-ticket-selling'); ?></option>
                                <option value="checkbox"><?php esc_html_e('Checkbox', 'dream-online-ticket-selling'); ?></option>
                                <option value="date"><?php esc_html_e('Date Picker', 'dream-online-ticket-selling'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="field_options_row" style="display: none;">
                        <th><label for="field_options"><?php esc_html_e('Options', 'dream-online-ticket-selling'); ?></label></th>
                        <td>
                            <textarea id="field_options" name="field_options" class="large-text" rows="3"></textarea>
                            <p class="description"><?php esc_html_e('For dropdown/checkbox: one option per line', 'dream-online-ticket-selling'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="is_required"><?php esc_html_e('Required', 'dream-online-ticket-selling'); ?></label></th>
                        <td><input type="checkbox" id="is_required" name="is_required" value="1"></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e('Add Field', 'dream-online-ticket-selling'); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div id="dots-field-modal" class="dots-modal" style="display: none;">
    <div class="dots-modal-content">
        <span class="dots-modal-close">&times;</span>
        <h2><?php esc_html_e('Edit Field', 'dream-online-ticket-selling'); ?></h2>
        <form id="dots-field-edit-form">
            <input type="hidden" id="edit_field_id" name="field_id">
            <table class="form-table">
                <tr>
                    <th><label for="edit_field_name"><?php esc_html_e('Field Name', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="edit_field_name" name="field_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_field_label"><?php esc_html_e('Field Label', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="edit_field_label" name="field_label" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_field_type"><?php esc_html_e('Field Type', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="edit_field_type" name="field_type" required>
                            <option value="text"><?php esc_html_e('Text', 'dream-online-ticket-selling'); ?></option>
                            <option value="email"><?php esc_html_e('Email', 'dream-online-ticket-selling'); ?></option>
                            <option value="tel"><?php esc_html_e('Phone', 'dream-online-ticket-selling'); ?></option>
                            <option value="textarea"><?php esc_html_e('Textarea', 'dream-online-ticket-selling'); ?></option>
                            <option value="select"><?php esc_html_e('Dropdown', 'dream-online-ticket-selling'); ?></option>
                            <option value="checkbox"><?php esc_html_e('Checkbox', 'dream-online-ticket-selling'); ?></option>
                            <option value="date"><?php esc_html_e('Date Picker', 'dream-online-ticket-selling'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr id="edit_field_options_row" style="display: none;">
                    <th><label for="edit_field_options"><?php esc_html_e('Options', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <textarea id="edit_field_options" name="field_options" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="edit_is_required"><?php esc_html_e('Required', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="edit_is_required" name="is_required" value="1"></td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary"><?php esc_html_e('Update Field', 'dream-online-ticket-selling'); ?></button>
                <button type="button" class="button dots-modal-close"><?php esc_html_e('Cancel', 'dream-online-ticket-selling'); ?></button>
            </p>
        </form>
    </div>
</div>

