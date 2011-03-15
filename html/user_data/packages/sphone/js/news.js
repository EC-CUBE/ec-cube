$(function() {
      $('.anews').each(function(i) {
                           $(this).addClass('news-' + i).hide();
                       });
      $('.news-0').fadeIn('slow');
      var count = 1;
      var interval = 5000;
      var timer = setInterval(function() {
                                  $('.anews').hide();
                                  $('.news-' + count).fadeIn('slow');
                                  count++;
                                  if (count > $('.anews').size() -1) {
                                      count = 0;
                                  }
                              }, interval);

  });
