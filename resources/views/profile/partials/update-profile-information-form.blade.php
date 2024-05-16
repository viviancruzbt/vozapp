<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informação do Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Atualize aqui os dados da sua conta.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="mt-4">
            <label for="googleid" class="block font-medium text-sm text-gray-700">{{ __('Google Analytics ID') }}</label>
            <input id="googleid" class="block mt-1 w-full" type="text" name="googleid" value="{{ old('googleid', $user->googleid ?? '') }}" autofocus />
        </div>

        <!-- Campo Custom Event -->
        <div class="mt-4">
            <label for="custom_event" class="block font-medium text-sm text-gray-700">{{ __('Conversão Principal') }}</label>
            <input id="custom_event" class="block mt-1 w-full" type="text" name="custom_event" value="{{ old('custom_event', $user->custom_event ?? '') }}" autofocus />
        </div>
        <!-- Campo Custom Event -->
        <div class="mt-4">
            <label for="evento_2" class="block font-medium text-sm text-gray-700">{{ __('Evento de Meio de Funil') }}</label>
            <input id="evento_2" class="block mt-1 w-full" type="text" name="evento_2" value="{{ old('evento_2', $user->evento_2 ?? '') }}" autofocus />
        </div>
        <!-- Campo Custom Event -->
        <div class="mt-4">
            <label for="evento_3" class="block font-medium text-sm text-gray-700">{{ __('Evento de topo de Funil') }}</label>
            <input id="evento_3" class="block mt-1 w-full" type="text" name="evento_3" value="{{ old('evento_3', $user->evento_3 ?? '') }}" autofocus />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Seu e-mail não está verificado.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Um novo link de verificação foi enviado para seu e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salvar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                
                
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Salvo.') }}</p>
            @endif
        </div>
    </form>
</section>


