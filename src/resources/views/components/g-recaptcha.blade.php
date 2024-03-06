@props(['action'])
<div x-data="gRecaptcha" x-init="$nextTick(()=>getResponseToken())">
    <input type="hidden"  {{$attributes}}>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gRecaptcha', () => {
            return ({
                gRecaptchaResponse: @entangle($attributes->wire('model')),
                getResponseToken() {
                    let self= this;
                    grecaptcha.execute(@js(config('recaptchav3.sitekey')), {action: '{{$action}}'}).then(function(token) {
                        self.gRecaptchaResponse = token;
                    });
                },
            });
        })
    })

</script>