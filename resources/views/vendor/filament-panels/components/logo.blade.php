@if(!empty(site_config('site_logo')) && gettype(site_config('site_logo'))=="int")
<div class="aspect-video w-64">
  <x-curator-glider
      class="img-responsive img-fullwidth h-8 w-10"
      :media="site_config('site_logo')"
      glide=""
      alt="Logo"
      fallback="thumbnail"
      data-bgposition="center 10%"
      data-bgfit="cover" data-bgrepeat="no-repeat" data-no-retina
  />
</div>
@endif
