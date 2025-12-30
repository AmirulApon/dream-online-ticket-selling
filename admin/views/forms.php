<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap dots-forms">
    <h1><?php _e('Ticket Form Management', 'dream-ticket'); ?></h1>
    <p><?php _e('Customize the fields that appear in the ticket purchase form.', 'dream-ticket'); ?></p>
    
    <div class="dots-forms-grid">
        <div class="dots-forms-main">
            <h2><?php _e('Form Fields', 'dream-ticket'); ?></h2>
            <p><?php _e('Drag and drop to reorder fields.', 'dream-ticket'); ?></p>
            
            <div id="dots-fields-list" class="dots-sortable-fields">
                <?php if (!empty($fields)): ?>
                    <?php foreach ($fields as $field): ?>
                        <div class="dots-field-item" data-field-id="<?php echo $field->id; ?>">
                            <span class="dashicons dashicons-menu"></span>
                            <div class="dots-field-info">
                                <strong><?php echo esc_html($field->field_label); ?></strong>
                                <span class="dots-field-type"><?php echo esc_html($field->field_type); ?></span>
                                <?php if ($field->is_required): ?>
                                    <span class="dots-field-required"><?php _e('Required', 'dream-ticket'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="dots-field-actions">
                                <button type="button" class="button dots-edit-field" data-field-id="<?php echo $field->id; ?>">
                                    <?php _e('Edit', 'dream-ticket'); ?>
                                </button>
                                <button type="button" class="button dots-delete-field" data-field-id="<?php echo $field->id; ?>">
                                    <?php _e('Delete', 'dream-ticket'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php _e('No custom fields yet. Add your first field below.', 'dream-ticket'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dots-forms-sidebar">
            <h2><?php _e('Add New Field', 'dream-ticket'); ?></h2>
            <form id="dots-field-form">
                <table class="form-table">
                    <tr>
                        <th><label for="field_name"><?php _e('Field Name', 'dream-ticket'); ?></label></th>
                        <td>
                            <input type="text" id="field_name" name="field_name" class="regular-text" required>
                            <p class="description"><?php _e('Internal field name (lowercase, no spaces)', 'dream-ticket'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="field_label"><?php _e('Field Label', 'dream-ticket'); ?></label></th>
                        <td><input type="text" id="field_label" name="field_label" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="field_type"><?php _e('Field Type', 'dream-ticket'); ?></label></th>
                        <td>
                            <select id="field_type" name="field_type" required>
                                <option value="text"><?php _e('Text', 'dream-ticket'); ?></option>
                                <option value="email"><?php _e('Email', 'dream-ticket'); ?></option>
                                <option value="tel"><?php _e('Phone', 'dream-ticket'); ?></option>
                                <option value="textarea"><?php _e('Textarea', 'dream-ticket'); ?></option>
                                <option value="select"><?php _e('Dropdown', 'dream-ticket'); ?></option>
                                <option value="checkbox"><?php _e('Checkbox', 'dream-ticket'); ?></option>
                                <option value="date"><?php _e('Date Picker', 'dream-ticket'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="field_options_row" style="display: none;">
                        <th><label for="field_options"><?php _e('Options', 'dream-ticket'); ?></label></th>
                        <td>
                            <textarea id="field_options" name="field_options" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('For dropdown/checkbox: one option per line', 'dream-ticket'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="is_required"><?php _e('Required', 'dream-ticket'); ?></label></th>
                        <td><input type="checkbox" id="is_required" name="is_required" value="1"></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Add Field', 'dream-ticket'); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div id="dots-field-modal" class="dots-modal" style="display: none;">
    <div class="dots-modal-content">
        <span class="dots-modal-close">&times;</span>
        <h2><?php _e('Edit Field', 'dream-ticket'); ?></h2>
        <form id="dots-field-edit-form">
            <input type="hidden" id="edit_field_id" name="field_id">
            <table class="form-table">
                <tr>
                    <th><label for="edit_field_name"><?php _e('Field Name', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="edit_field_name" name="field_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_field_label"><?php _e('Field Label', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="edit_field_label" name="field_label" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_field_type"><?php _e('Field Type', 'dream-ticket'); ?></label></th>
                    <td>
                        <select id="edit_field_type" name="field_type" required>
                            <option value="text"><?php _e('Text', 'dream-ticket'); ?></option>
                            <option value="email"><?php _e('Email', 'dream-ticket'); ?></option>
                            <option value="tel"><?php _e('Phone', 'dream-ticket'); ?></option>
                            <option value="textarea"><?php _e('Textarea', 'dream-ticket'); ?></option>
                            <option value="select"><?php _e('Dropdown', 'dream-ticket'); ?></option>
                            <option value="checkbox"><?php _e('Checkbox', 'dream-ticket'); ?></option>
                            <option value="date"><?php _e('Date Picker', 'dream-ticket'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr id="edit_field_options_row" style="display: none;">
                    <th><label for="edit_field_options"><?php _e('Options', 'dream-ticket'); ?></label></th>
                    <td>
                        <textarea id="edit_field_options" name="field_options" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="edit_is_required"><?php _e('Required', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="edit_is_required" name="is_required" value="1"></td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary"><?php _e('Update Field', 'dream-ticket'); ?></button>
                <button type="button" class="button dots-modal-close"><?php _e('Cancel', 'dream-ticket'); ?></button>
            </p>
        </form>
    </div>
</div>

