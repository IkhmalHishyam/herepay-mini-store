<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
 status?: string;
 canResetPassword: boolean;
}>();
</script>

<template>
  <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
      {{ status }}
    </div>



    <Form
      v-bind="AuthenticatedSessionController.store.form()"
      :reset-on-success="['password']"
      v-slot="{ errors, processing }"
      class="flex flex-col gap-6"
    >
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input
            id="email"
            type="email"
            name="email"
            required
            autofocus
            :tabindex="1"
            autocomplete="email"
            placeholder="email@example.com"
          />
          <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
          <div class="flex items-center justify-between">
            <Label for="password">Password</Label>
            <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5">
              Forgot password?
            </TextLink>
          </div>
          <Input
            id="password"
            type="password"
            name="password"
            required
            :tabindex="2"
            autocomplete="current-password"
            placeholder="Password"
          />
          <InputError :message="errors.password" />
        </div>

        <div class="flex items-center justify-between">
          <Label for="remember" class="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span>Remember me</span>
          </Label>
        </div>

        <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing">
          <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
          Log in
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Don't have an account?
        <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
      </div>
    </Form>
  </AuthBase>

  <!-- Floating Credentials Box -->
  <div class="fixed bottom-6 right-6 z-50">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-xs">
      <div class="flex items-center gap-2 mb-3">
        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium text-gray-700">Test Credentials</span>
      </div>
      <div class="space-y-2 text-xs text-gray-600">
        <div>
          <span class="font-medium text-gray-700">Admin:</span>
          <div class="mt-1 space-y-1">
            <div class="bg-gray-50 px-2 py-1 rounded border text-gray-600">admin@herepay.com</div>
            <div class="bg-gray-50 px-2 py-1 rounded border text-gray-600">password</div>
          </div>
        </div>
        <div>
          <span class="font-medium text-gray-700">Customer:</span>
          <div class="mt-1 space-y-1">
            <div class="bg-gray-50 px-2 py-1 rounded border text-gray-600">customer@herepay.com</div>
            <div class="bg-gray-50 px-2 py-1 rounded border text-gray-600">password</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
