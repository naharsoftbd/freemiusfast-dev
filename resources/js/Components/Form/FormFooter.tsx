import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-react';

export default function FormFooter({ handleCancel, processing, submitTitle }) {
    return (
        <div className="flex justify-start gap-2">
            {handleCancel && <Button type="button" onClick={() => handleCancel()} className="mt-2 w-fit" disabled={processing}>
                Cancel
            </Button>
            }
            <Button type="submit" className="mt-2 w-fit" disabled={processing}>
                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                {submitTitle}
            </Button>
        </div>
    );
}
