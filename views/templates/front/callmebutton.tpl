{*
*  @ LICENSE
*    MIT License
*  @ LICENSE
*}
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div id="callmeback-form-msg" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display:none;">
            <div id="callmeback-form-msg-ok" class="alert alert-success" style="display:none;">
                {l s='Thank you for your interest, we will call you back ASAP.' mod='callmeback'}
            </div>
            <div id="callmeback-form-msg-alert" class="alert alert-danger" style="display:none;">
            </div>
        </div>
        <div class="buttons_bottom_block no-print col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <button id="callmeback" class="exclusive button btn btn-default">
                {l s='Call me back' mod='callmeback'}
                {* <img src="{$callmeback_config.callmebackimg|escape:'htmlall':'UTF-8'}" alt="callmeback"> *}
            </button>
        </div>
        <div id="callmeback-form" style="display: none;" class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <form action="">
                <div class="form-group">
                    <label for="callmeback_name">{l s='Name' mod='callmeback'}</label>
                    <input type="text" class="form-control" name="callmeback_name" id="callmeback_name" placeholder="{l s='Name' mod='callmeback'}" required>
                </div>
                {if isset($callmeback_config.callmeback_surname)}
                    {if $callmeback_config.callmeback_surname eq 1}
                <div class="form-group">
                    <label for="callmeback_surname">{l s='Surname' mod='callmeback'}</label>
                    <input type="text" class="form-control" name="callmeback_surname" id="callmeback_surname" placeholder="{l s='Surname' mod='callmeback'}" required>
                </div>
                    {/if}
                {/if}
                {if isset($callmeback_config.callmeback_email)}
                    {if $callmeback_config.callmeback_email eq 1}
                <div class="form-group">
                    <label for="callmeback_email">{l s='Email' mod='callmeback'}</label>
                    <input type="email" class="form-control" name="callmeback_email" id="callmeback_email" placeholder="{l s='email@example.com' mod='callmeback'}" required>
                </div>
                    {/if}
                {/if}
                <div class="form-group">
                    <label for="callmeback_telephone">{l s='Telephone' mod='callmeback'}</label>
                    <input type="text" class="form-control" name="callmeback_telephone" id="callmeback_telephone" placeholder="{l s='Telephone ex. +30 699#######' mod='callmeback'}" required>
                </div>
                {if isset($callmeback_config.callmeback_telephone2)}
                    {if $callmeback_config.callmeback_telephone2 eq 1}
                <div class="form-group">
                    <label for="callmeback_telephone2">{l s='Telephone' mod='callmeback'}</label>
                    <input type="text" class="form-control" name="callmeback_telephone2" id="callmeback_telephone2" placeholder="{l s='Telephone 2 ex. +30 699#######' mod='callmeback'}">
                </div>
                    {/if}
                {/if}
                {if isset($callmeback_config.callmeback_hours)}
                    {if $callmeback_config.callmeback_hours eq 1}
                <div class="form-group">
                    {l s='Hours available to be called ' mod='callmeback'}
                </div>
                <div class="form-group">
                    <label for="callmeback_hours_from">{l s='From' mod='callmeback'}</label>
                    <input type="time" class="form-control" name="callmeback_hours_from" id="callmeback_hours_from">
                </div>
                <div class="form-group">
                    <label for="callmeback_hours_to">{l s='To' mod='callmeback'}</label>
                    <input type="time" class="form-control" name="callmeback_hours_to" id="callmeback_hours_to">
                </div>
                    {/if}
                {/if}
                {if isset($callmeback_config.callmeback_msg)}
                    {if $callmeback_config.callmeback_msg eq 1}
                    <div class="form-group">
                    <label for="callmeback_msg">{l s='Message' mod='callmeback'}</label>
                    <input type="text" class="form-control" name="callmeback_msg" id="callmeback_msg" placeholder="{l s='Leave us a message' mod='callmeback'}">
                </div>
                    {/if}
                {/if}
                <button id="callmeback-submit" class="exclusive button btn btn-default">{l s='Submit' mod='callmeback'}</button>
            </form>
        </div>
    </div>
</div>