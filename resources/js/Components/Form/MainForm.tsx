import FormFooter from '@/components/Form/FormFooter';

interface FormProps {
    handleSubmit: (e: React.FormEvent) => void;
    handleCancel: (e: React.FormEvent) => void;
    processing: boolean;
    submitTitle: string;
    children?: React.ReactNode;
}

export default function MainForm({ handleSubmit, handleCancel, processing, submitTitle, children }: FormProps) {
    return (
        <form role="form" className="bordr flex w-full flex-col gap-4 p-4 shadow-md sm:w-2/3 sm:rounded-lg" onSubmit={handleSubmit}>
            {children}
            <FormFooter handleCancel={handleCancel} processing={processing} submitTitle={submitTitle} />
        </form>
    );
}
