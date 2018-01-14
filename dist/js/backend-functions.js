$(document).ready(function(){
	$(document).on('keyup', '.search', function(){
		targetListElem = $(this).data('target');
		searchedElem = $(this).val();
		
		source = $('.'+targetListElem);
		source.find('a').hide();
		source.find('a:contains('+searchedElem+')').show();
	});


	$(document).on('submit', '.ajax-form', function(){
		formUrl = $(this).attr('action');
		formData = $(this).serialize();
		$.ajax({
			url: formUrl,
			data: formData,
			method: 'post',
			success: function(response){
				addAlert('alert-success', 'Workorder Updated');
				$('.modal').modal('hide');
			}
		});

		return false;
	});

	$('.cert_selector').click(function(){
		certificate_index = $(this).data('id');
		selected_certificates = selected_certificates || [];
		selected_certificates.push(certificate_list[certificate_index]);

		console.log(selected_certificates);
	});

	$('.fileDatePicker').flatpickr({
		enableTime: false,
		allowInput: false,
		altInput: true,
		minDate: 'today',
		dateFormat: 'Y-m-d',
		utc: true
	});

	$('.loadEditFile').click(function(){
		fileUrl = $(this).attr('href');
		$.ajax({
			'url': fileUrl,
			'method': 'GET',
			'dataType': 'html'
		}).done(function(response){
			$('body').append(response);
			$('#editFile').modal('show');
		});

		return false;
	});

	$('#changeProfileImage').change(function(){
    	readURL(this);
	});

	$('.select2').select2({
		theme: "bootstrap"
	});

	$('.vesselSelect').on("select2:select", function(){
		selected_val = $(this).val();
		$('input[name="Owner"]').val(vesselList[selected_val].Owner);
	});
	
	$('#editWorkOrder').trigger('click');
});
var params = [];

function setWorkOrderParams(paramName){
	params.push(paramName);
	return params;
	return false;
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#profileImage').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function setSelected(menuText){
	$('.left-menu span.name:exactMatch("'+menuText+"')").addClass('active');
};

var aAsc = [];
function sortTable(nr) {
	aAsc[nr] = aAsc[nr] == 'asc' ? 'desc' : 'asc';
	$('table>tbody>tr').tsort('td:eq('+nr+')',{order:aAsc[nr]});
	$('table>tbody>tr').removeClass('even');
	$('table>tbody>tr:even').addClass('even');
};

jQuery.expr[":"].contains = jQuery.expr.createPseudo(function(arg) {
    return function( elem ) {
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

jQuery.expr[':'].exactMatch = function(a, i, m){
    var match = $(a).text().match("^" + m[3] + "$")
    return match && match.length > 0;                                                                                                                                                                                                                                            
};
