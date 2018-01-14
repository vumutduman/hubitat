var firstSidebarScrollbar,
	secondSidebarScrollbar,
	mainContentScrollbar;

$(window).on('resize', function(){
	windowResizer();
});

function windowResizer() {
	setWidthsOfMainList();
}

// Setting the widths of main wrappers
function setWidthsOfMainList() {
	var firstSidebarW = $('.first-sidebar').outerWidth(true);

	if ($('.second-sidebar').length > 0) {
		var secondSidebarW = $('.second-sidebar').outerWidth(true);
		var mainContentW = firstSidebarW + secondSidebarW;
		$('.second-sidebar').css('left', firstSidebarW + 'px');
		$('.main-content').css('width', 'calc(100% - ' + mainContentW + 'px)');
		$('.second-sidebar-header').css('width', secondSidebarW + 'px');
	} else {
		$('.main-content').css('width', 'calc(100% - ' + firstSidebarW + 'px)');
	}
}

// Dropdown Arrow toogle
function dropdownActions() {
	$('.dropdown').on('show.bs.dropdown', function () {
		$(this).find('a > .hubitat').addClass('up-arrow-bold');
	});
	$('.dropdown').on('hide.bs.dropdown', function () {
		$(this).find('a > .hubitat').removeClass('up-arrow-bold');
	});
}

// Scrollbar Initializing
function initializeScrollBar() {
	if ($('.first-sidebar').length > 0) {
		firstSidebarScrollbar = $('.first-sidebar').mCustomScrollbar({
			theme: "minimal-dark",
			scrollInertia: 100
		});
	}
	if ($('.second-sidebar').length > 0) {
		secondSidebarScrollbar = $('.second-sidebar').mCustomScrollbar({
			theme: "minimal-dark",
			scrollInertia: 100
		});
		if($('.second-sidebar').find('.second-sidebar-actions').find('.search').length > 0) {
			$('#collapseSearch').on('shown.bs.collapse', function () {
				secondSidebarScrollbar.mCustomScrollbar('scrollTo', 'top');
				$('#collapseSearch').find('input').focus();
			});
			$('#collapseSearch').on('hidden.bs.collapse', function () {
				$('#collapseSearch').find('input').val("");
			});
		}
	}
	/*
	if ($('.main-content-wrapper').length > 0) {
		mainContentScrollbar = $('.main-content').mCustomScrollbar({
			theme: "minimal-dark",
			scrollInertia: 100
		});
	} */
}



// 
function certificateCollapse(d,e,f) {
	if($(d).length > 0) {
		$(d).on('show.bs.collapse', function () {
			$(e+' > a').find('.hubitat').removeClass('plus-bold-solid').addClass('cross-circle-bold-solid');
			$(e+' > a').addClass('open');
		});
		$(d).on('hide.bs.collapse', function () {
			$(e+' > a').find('.hubitat').removeClass('cross-circle-bold-solid').addClass('plus-bold-solid');
			$(e+' > a').removeClass('open');
		});

		// onClick event to UNDO button at Collapsed element
		$(e).find(f).on('click', function(){
			$(d).collapse('hide');
		});
	}
}

// --
function logsCollapse(e,f) {
	if($(e).length > 0) {
		$(e).on('show.bs.collapse', function () {
			$(f+' > a').find('.hubitat').css('transform', 'rotateX(180deg)');
		});
		$(e).on('hide.bs.collapse', function () {
			$(f+' > a').find('.hubitat').css('transform', 'rotateX(0deg)');
		});
	}
}


// --
function initializeCertificateSelector(e) {
	// Statuses [active, new-added, deleted]
	$(e).on('click', function() {
		var className = $(this).attr('class');
		var itemIndexID = parseInt($(this).attr('data-id'));
		switch(className) {
			case 'active':
			case 'active deleted':
				$(e+'[data-id='+itemIndexID+']').toggleClass('deleted');
				break;
			case 'new-added':
				$(e+'[data-id='+itemIndexID+']').toggleClass('new-added');
				break;
			case 'deleted':
				$(e+'[data-id='+itemIndexID+']').toggleClass('deleted');
				break;
			default:
				$(e+'[data-id='+itemIndexID+']').toggleClass('new-added');
		}
	});
}





/* ============================================= */
/* 			 Adding Alert - Starts 					 */

// Main function for Adding alert
function addAlert(f,g) {
	// f variable options 	: String : ['alert-warning', 'alert-success', 'alert-info', 'alert-danger', 'alert-dark']
	// g variable 				: String : HTML

	var _randomNumber = Math.floor((Math.random() * 1000000) + 1)
	var _id = '#alert' + _randomNumber;
	if($('.alert-module').length <= 0) {
		createAlertModule();
	}
	$('.alert-module').prepend(createAlertItem(_randomNumber,f,g));
	alertTimeOut(_id);
}

// Setting a time out for alert individually
function alertTimeOut(e) {
	setTimeout(function(){
		$(e).alert('close');
	}, 4000);

	// calling clear .alert-module function on complete
	$(e).on('closed.bs.alert', function () {
		clearAlertModule();
	})
}

// Clearing the .alert-module
function clearAlertModule() {
	if($('.alert-module').find('.alert').length < 1) {
		$('.alert-module').remove();
	}
}

// Creating an alert item based on the variables
function createAlertItem(x,y,z) {
	var _html = '<div id="alert' + x + '" class="alert ' + y +' alert-dismissible fade in" role="alert">';
	_html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" class="hubitat cross-lg-bold"></span></button>';
	_html += z;
	_html += '</div>';

	return(_html);
}

function createAlertModule() {
	var _html = '<div class="alert-module">';
	_html += '</div>';
	$('body').prepend(_html);
}

/* 			 Adding Alert - Ends 					 */
/* ============================================= */



// ---
function initializeEditWorkOrder(e,f,g) {
	if($(e).length > 0) {

		$(g).find('[readonly]').removeAttr('readonly');
		$(e).css('display', 'none');
		$(f).css('display', 'block');

		certificateCollapse('#addCertificate', '.module-404', '.cancel');
		initializeCertificateSelector('.list-404 > li');
		initializeAgencyChange();
	}
}
 function initializeAgencyChange() {
 	$('#workOrderAgencySettings').css('display', 'inline-block')
 }

//
function certificateEditMode(e) {
	var $obj = $(e);
	$(e).find('.custom-select').removeAttr('disabled');
	$obj.find('th[scope="row"]:last-child').css('display', 'table-cell');
	$obj.find('td[scope="row"]:last-child').css('display', 'table-cell');
	$obj.find('td[scope="action"]').css('display', 'table-cell');
}

function initializeWorkOrderPage() {
	logsCollapse('#logs', '.module-601');

	// Sadece Admin ve yetkilendirilen kişi yapabilecek
	$('#editWorkOrder').on('click', function(){
		initializeEditWorkOrder('#editWorkOrder', '.module-404', '.main-content-wrapper');
		certificateEditMode('.module-402');
		addAlert('alert-warning', '<p>Edit mode initialized.</p>');
	});

	// Sadece Admin ve yetkilendirilen kişi yapabilecek
	$('#completeWorkOrder').on('click', function(){
		addAlert('alert-danger', '<h4>Could not complete!</h4><p>Some uncompleted certificate(s) were found. Please complete that certificate(s) first.</p>');
	});
}

/* ============================================= */
/* Delete / Undo row on certificates - Starts 	 */

//
function deleteCertificateRow(e,f) {
	var dataID = e;
	var $obj = $(f).find('tr[data-id="' + dataID + '"]');
	var $btn = $obj.find('td').find('a');

	$obj.addClass('deleted');
	$obj.find('input').attr('readonly', 'true');

	$obj.find('.custom-select').attr('disabled', 'disabled'); // disables the selectboxes

	$btn.addClass('deleted');
	$btn.find('.hubitat').addClass('undo');
	addAlert('alert-danger', '<strong>&#x2715;</strong> Delete row #'+dataID);
}

//
function undoCertificateRow(e,f) {
	var dataID = e;
	var $obj = $(f).find('tr[data-id="' + dataID + '"]');
	var $btn = $obj.find('td').find('a');

	$obj.removeClass('deleted');
	$obj.find('input').removeAttr('readonly');

	$obj.find('.custom-select').removeAttr('disabled'); // enables the selectboxes

	$btn.removeClass('deleted');
	$btn.find('.hubitat').removeClass('undo');

	addAlert('alert-info', '<strong>&#x21a9;</strong> Undo row #'+dataID);
}

//
function undoDeleteCertificateRowInitializer(e) {
	var $td = $(e).find('td[scope="action"]');
	var $a = $td.find('a');

	$a.on('click', function(){
		var dataID = $(this).attr('data-id');
		var cn = $(this).attr('class');

		if(cn == 'deleted' || cn == 'newadded') {
			undoCertificateRow(dataID, e);
		} else {
			deleteCertificateRow(dataID, e);
		}
	});

}
/* Delete / Undo row on certificates - Ends 	 */
/* ============================================= */

function initilizeDatePickr() {
	if($('.datePickrWHour').length > 0) {
		$('.datePickrWHour').flatpickr({
			enableTime: true,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y - H:i",
			minuteIncrement: 15,
			time_24hr: true
		});
	}
	if($('.datePickrWHourStartToday').length > 0) {
		$('.datePickrWHourStartToday').flatpickr({
			enableTime: true,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y - H:i",
			minDate: 'today',
			minuteIncrement: 15,
			time_24hr: true
		});
	}
	if($('.datePickr').length > 0) {
		$('.datePickr').flatpickr({
			enableTime: false,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y"
		});
	}
	if($('.datePickrOnModal').length > 0) {
		$('.datePickrOnModal').flatpickr({
			enableTime: false,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y"
			//,appendTo: document.getElementsByClassName('modal fade in')
		});
	}
	if($('.datePickrStartToday').length > 0) {
		$('.datePickrStartToday').flatpickr({
			enableTime: false,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y",
			minDate: 'today'
		});
	}
	if($('.datePickrStartTodayOnModal').length > 0) {
		$('.datePickrStartTodayOnModal').flatpickr({
			enableTime: false,
			allowInput: false,
			altInput: true,
			altFormat: "j F Y",
			minDate: 'today'
			//,appendTo: document.getElementsByClassName('modal fade in')
		});
	}
}




function pageInitializer() {
	windowResizer();
	dropdownActions();
	initializeScrollBar();
	initializeWorkOrderPage();
	undoDeleteCertificateRowInitializer('.module-602');
	undoDeleteCertificateRowInitializer('.module-402');
}

$(document).on('ready', function(){
	pageInitializer();
	initilizeDatePickr();
});