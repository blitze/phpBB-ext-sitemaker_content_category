;(function($, window, document, undefined) {
	'use strict';

	$(document).ready(function() {
		var ajaxUrl = window.ajaxUrl || '';
		var groupId = window.groupId || 0;
		var lang = window.lang || {};
		var Twig = window.Twig || {};

		var dButtons = {};
		var groupAdmin = {};
		var dialogConfirmDelete = {};
		var currentGroupTitle = '';

		Twig.extendFunction('lang', function(value) {
			return (typeof lang[value] !== 'undefined') ? lang[value] : value;
		});

		// add group id to ajax requests
		$(document).ajaxSend(function(event, xhr, settings) {
			settings.url += (settings.url.indexOf('?') >= 0) ? '&' : '?';
			settings.url += 'group=' + groupId;
		});

		// Init icon picker
		$('.items-list').iconPicker({
			selector: '.icon-select',
			onSelect: function(item, iconClass) {
				var id = item.parentsUntil('li').parent().attr('id').substring(5);
				groupAdmin.treeBuilder('updateItem', {'cat_icon': iconClass}, id);
			}
		});

		// group list
		var groupDivObj = $('#sm-groups')
			.on('click', '.group-option', function(e) {
				groupId = +$(this).parent().attr('id').substring(6);
				$(this).parent().parent().children().removeClass('row3 current-group');
				$(this).parent().addClass('row3 current-group');
				groupAdmin.treeBuilder('getItems');
				e.preventDefault();
			})
			.on('click', '.group-edit', function(e) {
				var element = $(this).parent().prev().removeClass('group-option').parent().removeClass('current-group').find('.group-editable');
				currentGroupTitle = element.text();
				inlineGroupForm.show().appendTo(element.text('')).children(':input').val(currentGroupTitle).focus().select().end();
				e.preventDefault();
			})
			.on('click', '.group-delete', function(e) {
				dialogConfirmDelete.dialog({buttons: dButtons}).dialog('open');
				e.preventDefault();
			});

		var inlineGroupForm = $('<form id="inline-group-form"><input type="text" id="inline-group-edit" value="" /></form>').hide().appendTo($('body'));

		// manage group items
		groupAdmin = $('#nested-tree').treeBuilder({
			ajaxUrl: ajaxUrl,
			editForm: '#edit-group-item-form',
			dialogConfirm: '#dialog-confirm-group-item',
			fields: {
				itemId: 'cat_id',
				itemTitle: 'cat_name'
			}
		});

		$('.toggle-view').click(function(e) {
			$($(this).attr('href')).slideToggle();
			e.preventDefault();
		});

		// add new group
		$('#add-group').button().click(function(e) {
			groupId = 0;
			$.getJSON(ajaxUrl + 'add_group', function(data) {
				if (data.id === null) {
					return;
				}

				groupId = data.id;

				var html = '<li id="group-' + groupId + '" class="row3 current-group">';
				html += '<a href="#" class="group-option"><span class="group-editable">' + data.title + '</span></a>';
				html += '<div class="group-actions">';
				html += '<a href="#" class="group-delete left" title="' + lang.remove + '"><span class="ui-icon ui-icon-trash"></span></a>';
				html += '</span></li>';
				groupDivObj.children('li').removeClass('row3 current-group');
				groupDivObj.append(html);

				groupAdmin.show().treeBuilder('getItems');
			});

			e.preventDefault();
		});

		$('#inline-group-form').submit(function(e) {
			e.preventDefault();
			$(this).children('#inline-group-edit').trigger('blur');
		});

		$('#inline-group-edit').focusout(function(e) {
			var groupTitle = $(this).val();
			var element = $(this).val('').parent().parent();

			if (groupId && groupTitle && groupTitle !== currentGroupTitle) {
				$.post(ajaxUrl + 'edit_group', {'title': groupTitle}, function(group) {
					if (group.name) {
						element.text(group.name);
					}
				});
			} else {
				groupTitle = currentGroupTitle;
			}

			inlineGroupForm.hide().appendTo($('body'));
			element.text(groupTitle).parent().addClass('group-option').parent().addClass('current-group');
			e.preventDefault();
		});

		dButtons[lang.remove] = function() {
			if (groupId) {
				$.getJSON(ajaxUrl + 'delete_group', function(resp) {
					if (resp.id === groupId) {
						var group = $('#group-' + groupId);
						var up = group.prev();
						var down = group.next();

						if (up.length) {
							up.find('.group-option').trigger('click');
						} else if (down.length) {
							down.find('.group-option').trigger('click');
						} else {
							window.location.reload(false);
						}
						group.remove();
					}
				});
			}
			$(this).dialog('close');
		};

		dButtons[lang.cancel] = function() {
			$(this).dialog('close');
		};

		dialogConfirmDelete = $('#dialog-confirm-group').dialog({
			autoOpen: false,
			modal: true,
			width: 'auto',
			show: 'slide',
			hide: 'slide'
		});

		if (groupId) {
			groupAdmin.show();
		}
	});
})(jQuery, window, document);