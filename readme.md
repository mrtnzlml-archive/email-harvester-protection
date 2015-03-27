Nette email harvester protection
================================

I've tried few protections against email harvesting. And they all sucks. It's to easy to find email in the generated
source. Javascript protection is not good enough, because you can the JS code as well (it's the same output as HTML).
Look at tests.

What about images? Well... First harvester can have OCR, but this is not the main problem. Images are not user friendly.
You want to give email to your client but you are not allowing to copy it? Oh, come on...

Solution
--------

I am not sure if it's really solution, but... The main idea is that you should not get email address with the first
request. Otherwise you are sending email address to the harvester and it doesn't matter in which format. Harvester
programmer is not an idiot (probably)! So you should get email address via AJAX call on page load.

	<a id="protected">Loading...</a>
	<script>
        $(function () {
            $.nette.ajax({
                url: {link getEmail!},
                success: function (payload) {
                    console.log(payload.emailLink);
                    $('#protected').replaceWith(payload.emailLink);
                }
            });
        });
    </script>
    
Next you need request handler (naive implementation):

	public function handleGetEmail() {
        if ($this->isAjax()) {
            $hidden = 'protected@email.net';
            $el = Nette\Utils\Html::el('a')->href('mailto:' . $hidden)->setText($hidden);
            $this->payload->emailLink = (string)$el;
            $this->sendPayload();
        }
        $this->redirect('this');
    }

Yes, it's little bit complicated, but I am not sure how to get the email address now using harvester easily.

Competition
-----------

Try to write a test which can get email address from this protection method.
(It's possible but it's not that easy.) (-:
