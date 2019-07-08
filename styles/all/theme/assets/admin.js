/* global $ */
$(document).ready(function() {
	window.TreeGroup({
		idKey: 'group',
		ajaxUrl: window.config.ajaxUrl,
		dialogConfirm: '#dialog-confirm-delete-item',
		fields: {
			itemId: 'cat_id',
			itemTitle: 'cat_name',
			itemIcon: 'cat_icon'
		},
	});
});
