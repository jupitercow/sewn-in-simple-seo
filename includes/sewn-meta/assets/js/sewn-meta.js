jQuery(document).ready(function($) {

	var frame,
		$uploadFileLink = $('.sewn-upload-button'),
		targetTextField = '',
		postboxes = document.getElementsByClassName('sewn-postbox');

	function selectTab(e) {
		e.preventDefault();

		var $a = $(this),
			$group = $a.closest('.sewn-tab-wrap'),
			show = $a.data('tab'),
			current = '';

		// add and remove classes
		//$a.parent().addClass('sewn-tab-active').siblings().removeClass('sewn-tab-active');
		$($a[0].parentNode).addClass('sewn-tab-active').siblings().removeClass('sewn-tab-active');

		// Loop over all fields until you hit another group
		$group.nextUntil('.sewn-tab-wrap', '.sewn-field').each( function() {
			var $field = $(this);
			if ('tab' === $field.data('type')) {
				current = $field.data('name');
				// bail early if endpoint is found
				if ($field.hasClass('sewn-tab-endpoint')) {
					// stop loop - current tab group is complete
					return false;
				}
			}

			// show
			if (current === show) {
				// only show if hidden
				if ($field.hasClass('sewn-hidden-tab')) {
					$field.removeClass('sewn-hidden-tab');
				}
			// hide
			} else {
				// only hide if not hidden
				if (!$field.hasClass('sewn-hidden-tab')) {
					$field.addClass('sewn-hidden-tab');
				}
			}
		});
	}

	/**
	 * Tab support
	 */
	if (postboxes) {
		for (var i = 0, l = postboxes.length; i < l; i++) {
			var tabs = postboxes[i].getElementsByClassName('sewn-field-tab'),
				$inside = $('.inside', postboxes[i]),
				tabWrap = null,
				tabList = null,
				content = '',
				placement = '',
				tabItem = null,
				tabLink = null;

			// Add fields class
			//$inside.addClass('sewn-fields');

			// Set up the tabs
			if (tabs.length) {
				tabWrap = document.createElement('div');
				tabWrap.className = 'sewn-tab-wrap';
				tabList = document.createElement('ul');
				tabList.className = 'sewn-tab-group sewn-hl';
				tabWrap.appendChild(tabList);

				// Add each tab
				for (var j = 0, jl = tabs.length; j < jl; j++) {
					name = tabs[j].getAttribute('data-name');
					$currentTab = $('.sewn-tab', tabs[j]);
					content = $currentTab.text();
					placement = $currentTab.attr('data-placement');
					tabItem = document.createElement('li');

					tabLink = document.createElement('a');
					tabLink.className = 'sewn-tab-button';
					tabLink.href = '#' + name;
					tabLink.innerHTML = content;
					tabLink.setAttribute('data-tab', name);
					$(tabLink).on('click', selectTab);

					// If first, make active
					if (j === 0) {
						tabWrap.classList.add('sewn-tabs-' + placement);
						if ('left' === placement) {
							$inside.addClass('sewn-fields-sidebar');
						}
					}

					tabItem.appendChild(tabLink);
					tabList.appendChild(tabItem);
				}

				// Add the tabs to the box
				$inside.prepend(tabWrap);

				$('.sewn-tab-group li:first a').trigger('click');
			}
		}
	}

	/**
	 * Upload support
	 */
	$uploadFileLink.on('click', function(e) {
		e.preventDefault();

		targetTextField = this.getAttribute('data-target');

		// If the media frame already exists, reopen it
		if ( frame ) {
			frame.open();
			return;
		// Otherwise create a new media frame
		} else {
			frame = wp.media({
				title: 'Select or Upload Media',
				button: {
					text: 'Use this media'
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});
			// When an image is selected in the media frame
			frame.on('select', function() {
				// Get media attachment details from the frame state
				var attachment = frame.state().get('selection').first().toJSON();
				// Send the attachment URL to our custom image input field.
				$('#' + targetTextField).val(attachment.url);
			});
			// Open the modal on click
			frame.open();
		}
	});

	/**
	 * Add UI Enhancements
	 */
	function addSelect2() {
		$('select[data-sewn="1"]').each(function() {
			if ($(this).is('[data-ui="1"]')) {
				$(this).select2();
			}
		});
	}
	addSelect2();

});
