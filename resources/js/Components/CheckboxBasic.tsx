"use client"

import * as React from "react"
import { Checkbox } from "@/components/ui/checkbox"
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldGroup,
    FieldLabel,
} from "@/components/ui/field"

interface CheckboxBasicProps {
    setData: (key: string, value: boolean) => void
    defaultChecked?: boolean
    label: string
    name: string
    description?: string
}

export function CheckboxBasic({
    setData,
    defaultChecked = false,
    label,
    name,
    description,
}: CheckboxBasicProps) {

    const handleChange = (checked: boolean) => {
        setData(name, checked)
    }

    return (
        <FieldGroup className="max-w-sm">
            <Field orientation="horizontal">
                <Checkbox
                    id={name}
                    name={name}
                    defaultChecked={defaultChecked}
                    onCheckedChange={handleChange}
                />
                <FieldContent>
                    <FieldLabel htmlFor={name}>
                        {label}
                    </FieldLabel>
                    {description && (
                        <FieldDescription>
                            {description}
                        </FieldDescription>
                    )}
                </FieldContent>
            </Field>
        </FieldGroup>
    )
}
