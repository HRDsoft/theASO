var baseUrl = window.location.origin;
console.log(baseUrl);
$('.ajax-select2-keyword-id').select2({
    minimumInputLength: 1,
    quietMillis: 50,
    ajax: {
        url: baseUrl+'/admin/keyword/list',
        type: 'POST',
        headers: {'X-CRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        dataType: 'json',
        processResults: function (data) {
            console.log(data);
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            };
        }
    }
});

$(document).on('select2:select', 'select.ajax-select2-keyword-id', function(e){
    var data = e.params.data;
    var id = data.id;
    getRelatedKeywords(id)
    
});

function getRelatedKeywords(id){
    request = $.ajax({
        url: baseUrl+'/admin/keyword/'+id+'/related_keywords',
        type: 'GET',
        headers: {'X-CRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        cache: false,
        dataType: "json"

    });
    request.done(function (response, textStatus, jqXHR) {
        appendToRelatedKeywordsCard(response.related_keywords, id);
        appendToKeywordsCard(response.keywords);
    });
    request.fail(function (jqXHR, textStatus, errorThrown){
        console.log("The following error occurred: "+ jqXHR, textStatus, errorThrown);
    });
    request.always(function (response){
        console.log("To God Be The Glory...");
    });
}

function appendToKeywordsCard(keywords) {
    $('ul.keywords').empty();
    $.each(keywords, function(index, keyword){
        var li = `<li class="list-group-item p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="`+keyword.id+`" id="Keyword#`+keyword.id+`" `+(($.inArray(keyword.id, related_keywords_id) !== -1)?'checked':'')+`>
                            <label class="form-check-label" for="Keyword#`+keyword.id+`">
                                `+keyword.name+`
                            </label>
                        </div>
                    </li>`;
        $('ul.keywords').append(li);
    });
}
var related_keywords_id = [];
function appendToRelatedKeywordsCard(keywords, id) {
    $('ul.related-keywords').empty();
    $.each(keywords, function(index, keyword){
        var keyword = (keyword.keyword_id == id)?keyword.related_keyword:keyword.keyword;
        related_keywords_id.push(keyword.id);
        var li = `<li class="list-group-item p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="`+keyword.id+`" id="RelatedKeyword#`+keyword.id+`" checked>
                            <label class="form-check-label" for="RelatedKeyword#`+keyword.id+`">
                                `+keyword.name+`
                            </label>
                        </div>
                    </li>`;
        $('ul.related-keywords').append(li);
    });
}


(function($){
    $.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 1e3; // 1 second default timeout
            var timeoutReference,
                doneTyping = function(el){
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
            return this.each(function(i,el){
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress paste',function(e){
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too preemptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    if (e.type=='keyup' && e.keyCode!=8) return;
                    
                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });
})(jQuery);

$(document).on("focus", "input.keyword", function(){
    var scope = $(this);
    var input_group = $(scope).parent('div.input-group');
    var ul = $(input_group).siblings('ul');
    $(ul).find('li.please-enter').remove();
    var li = `<li class="list-group-item p-2 please-enter">
                        <div class="form-check">
                            <label class="form-check-label" for="RelatedKeyword">
                                Please enter 1 or more characters...
                            </label>
                        </div>
                    </li>`;
    $(ul).prepend(li);
});

$(document).on("blur", "input.keyword", function(){
    var scope = $(this);
    var input_group = $(scope).parent('div.input-group');
    var ul = $(input_group).siblings('ul');
    $(ul).find('li.please-enter').remove();
});

$(document).on("keyup", "input.keyword", function(){
    var scope = $(this);
    var input_group = $(scope).parent('div.input-group');
    var ul = $(input_group).siblings('ul');
    $(ul).find('li.please-enter label').text("Searching...");
});


$('input#keyword-addon').donetyping(function(){
    var scope = $(this);
    var value = $(scope).val();
    var data = {
        'term': value
    }
    request = $.ajax({
        url: baseUrl+'/admin/keyword/list',
        type: 'POST',
        headers: {'X-CRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: data,
        dataType: 'json',

    });
    request.done(function (response, textStatus, jqXHR) {
        if (response.length > 0) {
            $('ul.keywords').find('li.please-enter').remove();
            appendToKeywordsCard(response);
        }else{
            $('ul.keywords').find('li[class!="list-group-item p-2 please-enter"]').remove();
            $('ul.keywords').find('li.please-enter label').text("No result found");
        }
    });
    request.fail(function (jqXHR, textStatus, errorThrown){
        console.log("The following error occurred: "+ jqXHR, textStatus, errorThrown);
    });
    request.always(function (response){
        console.log("To God Be The Glory...");
    });
  // $('#example-output').text('Event last fired @ ' + (new Date().toUTCString()));
});

function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("related-keyword-addon");
    filter = input.value.toUpperCase();
    ul = document.getElementById("related-keywords");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        // a = li[i].getElementsByTagName("a")[0];
        txtValue = li[i].textContent || li[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}