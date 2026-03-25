import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useConfig } from "@/context/ConfigContext";
import { apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";
import { motion } from "framer-motion";

const schema = z.object({
  username: z.string().min(1, "Email or mobile is required"),
  code: z.string().min(4, "Code must be at least 4 digits").max(10),
});

type FormData = z.infer<typeof schema>;

export default function VerifyAccount() {
  const navigate = useNavigate();
  const { appConfig } = useConfig();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const isEmail =
    appConfig == null ? true : appConfig.register_method === "email";

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { username: "", code: "" },
  });

  const onSubmit = async (data: FormData) => {
    setError(null);
    setLoading(true);
    try {
      const res = await apiRaw.post<{ success?: boolean; message?: string }>(
        paths.auth.verification,
        {
          username: isEmail ? data.username.trim() : data.username.replace(/\D/g, ""),
          code: data.code.trim(),
        }
      );
      if (res?.success) {
        navigate("/login", { replace: true });
        return;
      }
      setError(res?.message ?? "Verification failed. Check the code and try again.");
    } catch (e) {
      setError(e instanceof Error ? e.message : "Verification failed.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <AnimatedBackground variant="grid">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="w-full max-w-md px-4"
      >
        <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
          <CardHeader className="space-y-1 text-center">
            <CardTitle className="text-2xl font-display">Verify your account</CardTitle>
            <CardDescription>
              Enter the verification code sent to your {isEmail ? "email" : "mobile"}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              {error && (
                <Alert variant="destructive">
                  <AlertDescription>{error}</AlertDescription>
                </Alert>
              )}
              <div className="space-y-2">
                <Label htmlFor="username">
                  {isEmail ? "Email" : "Mobile"}
                </Label>
                <Input
                  id="username"
                  type={isEmail ? "email" : "text"}
                  placeholder={isEmail ? "you@example.com" : "Mobile number"}
                  autoComplete={isEmail ? "email" : "tel"}
                  className="transition-shadow focus-visible:ring-2"
                  {...register("username")}
                />
                {errors.username && (
                  <p className="text-sm text-destructive">{errors.username.message}</p>
                )}
              </div>
              <div className="space-y-2">
                <Label htmlFor="code">Verification code</Label>
                <Input
                  id="code"
                  type="text"
                  inputMode="numeric"
                  placeholder="e.g. 12345"
                  autoComplete="one-time-code"
                  className="transition-shadow focus-visible:ring-2"
                  {...register("code")}
                />
                {errors.code && (
                  <p className="text-sm text-destructive">{errors.code.message}</p>
                )}
              </div>
              <Button
                type="submit"
                className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                disabled={loading}
              >
                {loading ? "Verifying…" : "Verify"}
              </Button>
            </form>
            <p className="mt-4 text-center text-sm text-muted-foreground">
              <Link to="/login" className="text-primary font-medium hover:underline">
                Back to Sign in
              </Link>
            </p>
          </CardContent>
        </Card>
      </motion.div>
    </AnimatedBackground>
  );
}
