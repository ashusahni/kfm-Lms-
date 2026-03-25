import { useState } from "react";
import { Link, useNavigate, useParams, useSearchParams } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { authService } from "@/services/auth";
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

const schema = z
  .object({
    email: z.string().min(1, "Email is required").email("Invalid email"),
    password: z.string().min(6, "At least 6 characters"),
    password_confirmation: z.string(),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: "Passwords must match",
    path: ["password_confirmation"],
  });

type FormData = z.infer<typeof schema>;

const ResetPassword = () => {
  const { token } = useParams<{ token: string }>();
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const emailFromUrl = searchParams.get("email") ?? "";

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      email: emailFromUrl,
      password: "",
      password_confirmation: "",
    },
  });

  const onSubmit = async (data: FormData) => {
    if (!token) return;
    setError(null);
    setLoading(true);
    try {
      const res = (await authService.resetPassword(token, {
        email: data.email.trim(),
        password: data.password,
        password_confirmation: data.password_confirmation,
      })) as { success?: boolean };
      if (res?.success) {
        navigate("/login", { replace: true });
        return;
      }
      setError("Failed to reset password. The link may have expired.");
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to reset password.");
    } finally {
      setLoading(false);
    }
  };

  if (!token) {
    return (
      <AnimatedBackground variant="dots">
        <Card className="w-full max-w-md border-border/80 bg-card/90 shadow-elevated backdrop-blur-md mx-4">
          <CardContent className="pt-6">
            <p className="text-destructive">Invalid reset link.</p>
            <Link to="/forgot-password">
              <Button className="mt-4 w-full bg-gradient-cta text-primary-foreground">Request new link</Button>
            </Link>
          </CardContent>
        </Card>
      </AnimatedBackground>
    );
  }

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
            <CardTitle className="text-2xl font-display">Set new password</CardTitle>
            <CardDescription>Enter your new password below</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              {error && (
                <Alert variant="destructive">
                  <AlertDescription>{error}</AlertDescription>
                </Alert>
              )}
              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  autoComplete="email"
                  placeholder="you@example.com"
                  className="transition-shadow focus-visible:ring-2"
                  {...register("email")}
                />
                {errors.email && (
                  <p className="text-sm text-destructive">{errors.email.message}</p>
                )}
              </div>
              <div className="space-y-2">
                <Label htmlFor="password">New password</Label>
                <Input
                  id="password"
                  type="password"
                  autoComplete="new-password"
                  className="transition-shadow focus-visible:ring-2"
                  {...register("password")}
                />
                {errors.password && (
                  <p className="text-sm text-destructive">{errors.password.message}</p>
                )}
              </div>
              <div className="space-y-2">
                <Label htmlFor="password_confirmation">Confirm password</Label>
                <Input
                  id="password_confirmation"
                  type="password"
                  autoComplete="new-password"
                  className="transition-shadow focus-visible:ring-2"
                  {...register("password_confirmation")}
                />
                {errors.password_confirmation && (
                  <p className="text-sm text-destructive">
                    {errors.password_confirmation.message}
                  </p>
                )}
              </div>
              <Button type="submit" className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90" disabled={loading}>
                {loading ? "Resetting…" : "Reset password"}
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
};

export default ResetPassword;
