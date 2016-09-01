define(["text!./home.html"], function(homeTemplate) {

  function HomeViewModel(params) {
      var self = this ;

      self.route = params.route ;

  }

  return { viewModel: HomeViewModel, template: homeTemplate };

});
