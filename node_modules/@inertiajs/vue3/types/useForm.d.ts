import { FormDataConvertible, FormDataKeys, Method, Progress, VisitOptions } from '@inertiajs/core';
type FormDataType = Record<string, FormDataConvertible>;
type FormOptions = Omit<VisitOptions, 'data'>;
export interface InertiaFormProps<TForm extends FormDataType> {
    isDirty: boolean;
    errors: Partial<Record<FormDataKeys<TForm>, string>>;
    hasErrors: boolean;
    processing: boolean;
    progress: Progress | null;
    wasSuccessful: boolean;
    recentlySuccessful: boolean;
    data(): TForm;
    transform(callback: (data: TForm) => object): this;
    defaults(): this;
    defaults(field: FormDataKeys<TForm>, value: FormDataConvertible): this;
    defaults(fields: Partial<TForm>): this;
    reset(...fields: FormDataKeys<TForm>[]): this;
    clearErrors(...fields: FormDataKeys<TForm>[]): this;
    setError(field: FormDataKeys<TForm>, value: string): this;
    setError(errors: Record<FormDataKeys<TForm>, string>): this;
    submit: (...args: [Method, string, FormOptions?] | [{
        url: string;
        method: Method;
    }, FormOptions?]) => void;
    get(url: string, options?: FormOptions): void;
    post(url: string, options?: FormOptions): void;
    put(url: string, options?: FormOptions): void;
    patch(url: string, options?: FormOptions): void;
    delete(url: string, options?: FormOptions): void;
    cancel(): void;
}
export type InertiaForm<TForm extends FormDataType> = TForm & InertiaFormProps<TForm>;
export default function useForm<TForm extends FormDataType>(data: TForm | (() => TForm)): InertiaForm<TForm>;
export default function useForm<TForm extends FormDataType>(rememberKey: string, data: TForm | (() => TForm)): InertiaForm<TForm>;
export {};
