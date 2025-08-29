import { toast as sonnerToast } from 'vue-sonner'

export type ToastType = 'default' | 'success' | 'error' | 'warning' | 'info'

export interface ToastOptions {
  id?: string | number
  title?: string
  description?: string
  duration?: number
  position?: 'top-left' | 'top-center' | 'top-right' | 'bottom-left' | 'bottom-center' | 'bottom-right'
  dismissible?: boolean
  action?: {
    label: string
    onClick: () => void
  }
}

export function useToast() {
  const toast = (message: string, options?: ToastOptions) => {
    return sonnerToast(message, {
      duration: options?.duration ?? 4000,
      dismissible: options?.dismissible ?? true,
      id: options?.id,
      description: options?.description,
      action: options?.action,
    })
  }

  const success = (message: string, options?: ToastOptions) => {
    return sonnerToast.success(message, {
      duration: options?.duration ?? 4000,
      dismissible: options?.dismissible ?? true,
      id: options?.id,
      description: options?.description,
      action: options?.action,
    })
  }

  const error = (message: string, options?: ToastOptions) => {
    return sonnerToast.error(message, {
      duration: options?.duration ?? 6000, // Longer duration for errors
      dismissible: options?.dismissible ?? true,
      id: options?.id,
      description: options?.description,
      action: options?.action,
    })
  }

  const warning = (message: string, options?: ToastOptions) => {
    return sonnerToast.warning(message, {
      duration: options?.duration ?? 5000,
      dismissible: options?.dismissible ?? true,
      id: options?.id,
      description: options?.description,
      action: options?.action,
    })
  }

  const info = (message: string, options?: ToastOptions) => {
    return sonnerToast.info(message, {
      duration: options?.duration ?? 4000,
      dismissible: options?.dismissible ?? true,
      id: options?.id,
      description: options?.description,
      action: options?.action,
    })
  }

  const loading = (message: string, options?: ToastOptions) => {
    return sonnerToast.loading(message, {
      id: options?.id,
      description: options?.description,
    })
  }

  const dismiss = (toastId?: string | number) => {
    if (toastId) {
      sonnerToast.dismiss(toastId)
    } else {
      sonnerToast.dismiss()
    }
  }

  return {
    toast,
    success,
    error,
    warning,
    info,
    loading,
    dismiss,
  }
}

// Global instance for use outside of components
export const { toast, success, error, warning, info, loading, dismiss } = useToast()